<?php

namespace App\Http\Controllers\Order;

use App\CardOfProduct;
use App\Code;
use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;
use App\Order;
use App\OrderDetails;
use App\Product;
use App\Shipping;
use App\Transformers\Order\Ar\Ar_OrderTransformer;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class MyOrderController extends ApiController
{

 


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $user= auth()->user();
        $orders = $user->orders()->get();
        if($request->language==='ar'){
            return $this->showAll($orders,Ar_OrderTransformer::class);
        }
        return $this->showAll($orders);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {     
        //return $this->getMessage('Comming Soon',404); 

        try{
            DB::beginTransaction();
            # get cart 
            $user= auth()->user();
            $cart = $user->card;
            # get products 
            $products = $cart->products()->get();
            if($products->first() === null){
                return $this->getMessage(__('orders.no_products'),404);
            }

            if($user->name === null){
                return $this->getMessage(__('orders.no_name'),404);
            }

            if($user->address === null){
                return $this->getMessage(__('orders.no_address'),404);
            }

            if($user->phone === null){
                return $this->getMessage(__('orders.no_phone'),404);
            }

            # make order
            $cart->discount = 0;
            $cart->save();
            $order = $this->add_order($products,$cart,$request->payMethod);
            # finish order 
            DB::commit();
            return $this->getMessage(__('order.created'),200);
        }catch(Exception $e){
            DB::rollBack();
            return $this->getMessage(__('orders.error'),402);
        }
    }

    public function show($id,Request $request)
    {
        $user= auth()->user();      
        $order = $user->orders()->findOrFail($id);
        if($request->language==='ar'){
            return $this->showOne($order,Ar_OrderTransformer::class);
        }
        return $this->showOne($order);
    }

    public function destroy($id)
    {
        try{
            DB::beginTransaction();
            $user= auth()->user();
            $order = $user->orders()->findOrFail($id);
            if((int)$order->done === 1){
                return $this->getMessage(__('order_cant_cancel'),409);
            }
            $details = OrderDetails::where('order_id',$order->id)->get();
            foreach($details as $detail){
                $product = Product::findOrFail($detail->product_id);
                $product->stock += $detail->quantity;
                $product->count_selled -= $detail->quantity;
                $product->save();
            }
            $order->done = 3;
            $order->save();
            DB::commit();
            return $this->getMessage(__('order_cancelled'),200);
        }catch(Exception $ex){
            return $this->getMessage(__('order_cancel_error'),404);
        }   
    }

    public function applyCode(Request $request){
        # validation 
        $rules = [
            "code" => "required|exists:codes,code",
        ];
        $validator = Validator::make($request->all(),$rules);
        if ($validator->fails()) {
            return $this->getMessage(__('order_code_error'),404);
        } 
        #end validation 

        $data = Code::where('code',$request->code)->first();

        # get cart 
        $user= auth()->user();
        $cart = $user->card;
        if((int)$cart->discount !== 0){
            return $this->getMessage(__('order_code_error'),404);
        }
        $cart->discount = $data->discount;
        $cart->save();
        $data->used = (int)$data->used + 1;
        $data->save();

        # get products 
        $details = CardOfProduct::where('card_id',$cart->id)->get();
        $total =0 ;
        foreach($details as $product_details){
            $product = Product::find($product_details->product_id);
            $total += $product_details->quantity * $product->price_discount;
        }

        #get discount 
        $discount = (int)$data->discount;
        $total = ($total * $discount)/100;
           
        #response
        $data = [
            'discount' => $total
        ];

        $data = [
            'data' => $data
        ];
        return $this->successResponse($data,200);
       
    }

    public function add_order($products,$cart){
        try{

            #create order
            $order = new Order();
            $order->user_id = auth()->user()->id;
            $amount = 0.00;
            $order->save();

            //shipping            
            $shipping_fees = Shipping::where('city',auth()->user()->city)->first();
            if($shipping_fees===null){
                $shipping_fees = 50;
            }else{
                $shipping_fees = $shipping_fees->shipping;
            }
            //end shipping

            # connect product to order and calcuate amount
            foreach($products as $product){
                $var = $this->storeDetails($order,$product,$cart);
                $amount += $var;
             }

           # check promo code
           if((int)$cart->discount !== 0){
                $order->origin_amount = $amount+$shipping_fees;
                $discount =  100-(int)$cart->discount;
                $amount = ($amount * $discount)/100;
            }

            # add shipping fees
            $order->amount = $amount + $shipping_fees;
            $order->shipping_fees = $shipping_fees;
       
            # add address of user
            $order->city = auth()->user()->city;
            $order->region=auth()->user()->region;
            $order->address=auth()->user()->address;
        
            #empty cart
            $card_products = CardOfProduct::where('card_id',$cart->id)->get();
            foreach($card_products as $card_product){
                $card_product->delete();
            }
            
            #create order num 
            $order->invoice_num = $this->createInvoiceNum($order);

            #save order into dp
            $order->save();
            return $order;
        }catch(Exception $e){
            return false;
        }
    }

    private function storeDetails($order,$product,$cart){
        $card_product = CardOfProduct::where('card_id',$cart->id)->where('product_id',$product->id)->first();
        $amount = $product->price_discount* $card_product->quantity;
        $order_details = new OrderDetails;
        $order_details->order_id = $order->id;
        $order_details->product_id = $product->id;
        $order_details->price = $product->price;
        $order_details->discount = $product->discount;
        $order_details->price_discount = $product->price_discount;
        $order_details->quantity = $card_product->quantity;
        $order_details->save();
        $product->count_selled += $card_product->quantity;
        $product->stock -= $card_product->quantity;
        $product->save();
        return $amount;
    }

    
    private function createInvoiceNum($order){
        $id = $order->id;
        $len = strlen((string)$id);
        for($i= 0 ; $i<6-$len ; $i++){
            $id = '0'.$id;
        }
        $date = new Carbon($order->created_at);  
        $id = $date->year.$date->month.$id;
        return $id;
    }
}
