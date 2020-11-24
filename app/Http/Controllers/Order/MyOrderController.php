<?php

namespace App\Http\Controllers\Order;

use App\CardOfProduct;
use App\Code;
use App\Exports\products;
use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;
use App\Order;
use App\OrderDetails;
use App\Product;
use App\Services\AcceptService;
use App\Services\R2SService;
use App\Shipping;
use App\Transformers\Order\Ar\Ar_OrderTransformer;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class MyOrderController extends ApiController
{

    public function __construct()
    {
        $this->middleware('auth:api')->except('acceptCallback','r2sCallback');
    }


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
            # validation 
            $rules = [
                "payMethod" => "required",
            ];
            $validator = Validator::make($request->all(),$rules);
            if ($validator->fails()) {
                return $this->getMessage('please insert type of payment (payMehtod)',403);
            } 
            #end validation 
            DB::beginTransaction();

            # get cart 
            $user= auth()->user();
            $cart = $user->card;
            # get products 
            $products = $cart->products()->get();
            if($products->first() === null){
                return $this->getMessage('you have no products to make order',404);
            }

            if($user->name === null){
                return $this->getMessage('you have no name , please complete your profile information',404);
            }

            if($user->userAddress === null){
                return $this->getMessage('you have no address to make order',404);
            }

            if($user->phone === null){
                return $this->getMessage('you have no phone to make order',404);
            }

            # make order
            $order = $this->add_order($products,$cart,$request->payMethod);
            # finish order 

            if($order){
                # CASH DEPEND ON SYSTEM
                if($request->payMethod === 'cash'){
                    $order->cash = 1;
                    $order->payment_type = "cash on delivery";
                    $data = [
                        'bill_reference' => 'order is done',
                        'redirect_url' => ''
                    ];
                    
                  // $this->handleR2S($user,$order,'PUD',$order->r2s_amount,'Cash');
                    $order->save();
                    # added all changes into database
                    DB::commit();
                    return $this->successResponse($data,200);
                }elseif($request->payMethod === 'creditCash'){
                    $order->payment_type = "credit on delivery ";
                    $order->cash = 2;
                    $data = [
                        'bill_reference' => 'order is done',
                        'redirect_url' => ''
                    ];
                    
                   // $this->handleR2S($user,$order,'PUDCC',$order->amount,'Credit Card');
                   $order->save();
                    # added all changes into database
                    DB::commit();
                    return $this->successResponse($data,200);
                }

                # ACCEPT COMPANIES INTEGRATION 
                
                # get integration id for the right method 
                $integration_id = $this->getIntgrationId($request->payMethod);

                # perpare order into accept intgration and get payment token 
                $token = $this->prepareOrder($order,$user,$integration_id);

                $response = '';
                # if order will done with card we don't need this step 
                if($request->payMethod !== 'card'){
                    # send request to send number for wallet and get code for kiosk
                    $response = $this->orderDone($request,$order,$token);
                    # response false mean there's something wrong with payment.
                    if($response === false){
                        return $this->getMessage('payment is fail please make sure your data.',402);
                    }
                }else{
                    $order->payment_type="card";
                }
                if($request->payMethod === 'kiosk'){
                   /* $r2s = new R2SService();
                    $Waybill = $r2s->createWaybill($user);
                    $order->waybill=$Waybill;*/
                    $order->save();
                }
                #save order into database 
                $order->save();
                $cart->discount = 0;
                $cart->save();
                #get redirect url 
                $redirect_url = $this->getRedirectUrl($token,$request->payMethod,$response);
                
                #create our order
                $data = [
                    'bill_reference' => $order->system_reference ?? '',
                    'redirect_url' => $redirect_url
                ];

                # added all changes into database
                DB::commit();
                return $this->successResponse($data,200);
            }else{
                # something error and rollback all data from database
                DB::rollBack();
                return $this->getMessage('something goes wrong',404);
            }

        }catch(Exception $e){
            DB::rollBack();
            return $this->getMessage('payment is fail please make sure of your phone is correct.',402);
        }
    }

    public function orderDone(Request $request,$order,$token){
        try{
            $accept = new AcceptService();
            if($request->payMethod === 'kiosk'){
                $response = $accept->kioskPay($token);
                $order->payment_type = 'kiosk';
                $order->system_reference = $response->data->bill_reference;
            }elseif($request->payMethod === 'wallet'){
               $response = $accept->mobileWalletsPay($token,$request->phone);
               $order->payment_type = 'wallet';
            }
 
            if($response->pending === true){
                $order->done = 4;  
            }else{
                $order->done = 2;
                $this->delete_order($order);
                return false;
            }
            $order->save();
            return $response;
        }catch(Exception $ex){
            return false; 
        }
        
    }

    public function acceptCallback(Request $request ){
        try{
            DB::beginTransaction();
            $success = $request->success;
            $order_id = $request->order;
            $order = Order::where('num',$order_id)->first();
            if($success === 'true'){
                $order->done = 1;  
                if($order->payment_type === 'card' || $order->payment_type === 'wallet'){
                    $user = $order->user;
                    $card = $user->card;
                    $details = OrderDetails::where('order_id',$order->id)->get();
                    foreach($details as $detail){
                        $product = Product::findOrFail($detail->product_id);
                        $product->stock -= $detail->quantity;
                        $product->count_selled += $detail->quantity;
                        $product->save();
                    }
    
                    $card_products = CardOfProduct::where('card_id',$card->id)->get();
                    foreach($card_products as $card_product){
                        $card_product->delete();
                    }
                  // $this->handleR2S($user,$order,'PUD',0,'Cash');
                }
            }else{
                $order->done = 2;
                $this->delete_order($order);
                DB::commit();
                return redirect('https://www.egyptonlineoutlet.com/#/ecommerce/payment-failed');
            }
            $order->save();
            DB::commit();
            return redirect('https://www.egyptonlineoutlet.com/#/ecommerce/thank-you');
        }catch(Exception $ex){
            DB::rollback();
            return redirect('https://www.egyptonlineoutlet.com/#/ecommerce/payment-failed');
        }
       
       // return redirect('https://www.egyptonlineoutlet.com/#/ecommerce/thank-you');
    }


    public function r2sCallback(Request $request){
       $waybill =  $request->waybillNumber;
       $order = Order::where('waybill',$waybill)->first();
       if($order){
        $order->tracing_status = $this->mappingSentenceOfR2s($request->status);
        $order->tracing_date = $request->modifiedOn;
        $order->save();
        return $this->getMessage('order is updated',200);
       }
       return $this->getMessage('Order is not found',404);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id,Request $request)
    {
        $user= auth()->user();      
        $order = $user->orders()->findOrFail($id);
        if($request->language==='ar'){
            return $this->showOne($order,Ar_OrderTransformer::class);
        }
        return $this->showOne($order);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function getIntgrationId($payMethod)
    {
        if($payMethod === 'card'){
            $integration_id = "86036";
        }elseif($payMethod === 'wallet'){
            $integration_id = "86035";
        }else{
            $integration_id = "86034";
        }
        return $integration_id;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $order = Order::findOrFail($id);
        if($order->done === 0){
            $order_details = OrderDetails::where('order_id',$order->id)->get();
            foreach($order_details as $order_detail){
                $order_detail->delete();
            }
            $order->delete();
            return $this->getMessage('your order has been cancelled',200);
        }
        return $this->getMessage('your order has been done , cannot cancel.',409);
    }

    public function applyCode(Request $request){
        # validation 
        $rules = [
            "code" => "required|exists:codes,code",
        ];
        $validator = Validator::make($request->all(),$rules);
        if ($validator->fails()) {
            return $this->getMessage('code is not correct, please try again',404);
        } 
        $data = Code::where('code',$request->code)->first();

        # get cart 
        $user= auth()->user();
        $cart = $user->card;
        if((int)$cart->discount !== 0){
            return $this->getMessage('used code already.',404);
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

        $discount = (int)$data->discount;
        $total = ($total * $discount)/100;
           
        $data = [
            'discount' => $total
        ];

        $data = [
            'data' => $data
        ];
        return $this->successResponse($data,200);
        #end validation 
    }
    public function add_order($products,$cart,$payMethod){
        try{
            $order = new Order();
            $order->user_id = auth()->user()->id;
            $order->amount=0.00;
            $amount = 0.00;
            $order->save();
            $r2s_amount=0.00;
            foreach($products as $product){
               $var = $this->storeDetails($order,$product,$cart,$payMethod);
               $amount += $var;
               if($product->company_name === 'WAHA'){
                    $r2s_amount +=$var;
                }
            }
           
            //shipping
            if(auth()->user()->userAddress === null){
                $shipping_fees = 50;
            }else{
                $shipping_fees = Shipping::where('city',auth()->user()->userAddress->region)->first();
                if($shipping_fees===null){
                    $shipping_fees = 50;
                }else{
                    $shipping_fees = $shipping_fees->shipping;
                }
            }
            // end shipping
           // $order->amount = $amount + $shipping_fees;

           if((int)$cart->discount !== 0){
                $order->origin_amount = $amount+$shipping_fees;
                $discount =  100-(int)$cart->discount;
                $amount = ($amount * $discount)/100;
            }

            $order->amount = $amount + $shipping_fees;
            $order->r2s_amount = $r2s_amount +$shipping_fees;
            $order->shipping_fees = $shipping_fees;
            $address= auth()->user()->userAddress;
            if($address !== null){
                $order->city = $address->city;
                $order->region=$address->region;
                $order->address=$address->address;
            }
            
            if($order->save()){
                if($payMethod === 'card' || $payMethod === 'wallet'){
                }else{
                    $card = auth()->user()->card;
                    $card_products = CardOfProduct::where('card_id',$card->id)->get();
                    foreach($card_products as $card_product){
                        $card_product->delete();
                    }
                }
               
                $order->invoice_num = $this->createInvoiceNum($order);
                $order->save();
                return $order;
            }
            #----------
        }catch(Exception $e){
            
            return false;
        }
    }

    private function storeDetails($order,$product,$cart,$payMethod){
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
        if($payMethod === 'card' || $payMethod === 'wallet'){
        }else{
            $product->count_selled += $card_product->quantity;
            $product->stock -= $card_product->quantity;
        }
        $product->save();
        return $amount;
    }


    private function delete_order($order){
        try{
            DB::beginTransaction();
            $details = OrderDetails::where('order_id',$order->id)->get();
            if($order->payment_type === 'card' || $order->payment_type==='wallet'){
                foreach($details as $detail){
                    $detail->delete();
                }
            }else{
                foreach($details as $detail){
                    $product = Product::findOrFail($detail->product_id);
                    $product->stock += $detail->quantity;
                    $product->count_selled -= $detail->quantity;
                    $product->save();
                    $detail->delete();
                }
            }
            $order->delete();
            DB::commit();
        }catch(Exception $ex){
            return false;
        }   
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

    private function prepareOrder($order,$user,$integration_id){
        $accept = new AcceptService();
        return $accept->handlePayment($order,$user,$integration_id);
    }


    private function getRedirectUrl($token,$payMethod,$response){
        switch($payMethod){

            # credit 
            case "card":
               $redirect_url =  $this->getCardRedirectUrl($token);
            break;

            # wallet
            case "wallet":
                $redirect_url = $response->iframe_redirection_url;
            break;

            # kiosk 
            case "kiosk":
                $redirect_url = '';
            break;
            
        }
        return $redirect_url ;
    }


    private function getCardRedirectUrl($token){
       return "https://accept.paymob.com/api/acceptance/iframes/70323?payment_token=$token";
    }
    


    private function handleR2S($user,$order,$pud,$amount,$payment_mode){
       /* $r2s = new R2SService();
        $Waybill = $r2s->createWaybill($user,$pud,$amount,$payment_mode);
        $order->waybill=$Waybill;
        $order->save();*/
        return true;
    }

    private function mappingSentenceOfR2s($key){
        return $this->map[$key];
    }


}
