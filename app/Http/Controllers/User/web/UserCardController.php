<?php

namespace App\Http\Controllers\User\web;

use App\Card;
use App\CardOfProduct;
use App\Event;
use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;
use App\Product;
use App\Transformers\Card\Ar\Ar_CardTransformer;
use Exception;
use Illuminate\Support\Facades\DB;

class UserCardController extends ApiController
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $card = $user->card;
        if($card === null){
            $card = new Card();
            $card->user_id = $user->id;
            $card->save();
        }
        $card->changed = 0;
        $card->save();

        $cart = auth()->user()->card;
        $products = $cart->products()->get();
        foreach($products as $product){  
            $card_product = CardOfProduct::where('card_id',$cart->id)->where('product_id',$product->id)->first();
            if($product->stock < $card_product->quantity){
                $cart->changed = 1;
                $cart->save();
                if($product->stock < 0){
                    $card_product->quantity = 0;
                    $cart->products()->detach($product->id);
                    
                }else{
                    $card_product->quantity=$product->stock;
                }
                $card_product->save();
            }
        }

        if($request->language==='ar'){
            return $this->showOne($card,Ar_CardTransformer::class);
        }else{
            return $this->showOne($card);
        }
       
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $user = auth()->user();
        $card = $user->card;
        if($card === null){
            $card = new Card();
            $card->user_id = $user->id;
            $card->save();
        }
        $product = Product::findOrFail($request->product_id);
        if($product->stock < $request->quantity){
            return $this->getMessage('out of stock',402);
        }
        $card = $this->add_product_card($request,$card);
        if($card){
            return $this->showOne($card);
        }
        return $this->getMessage('this product is limited you can only .',402);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = auth()->user();
        $cart = $user->card;
        $product = Product::findOrFail($id);
        $cart->products()->detach($product->id);
        return $this->showOne($cart);
        return $this->getMessage('item has been removed form cart',200);
    }
    
    protected function add_product_card($request,$card){
        try{
            DB::beginTransaction();
            $product = Product::findOrFail($request->product_id);
            $card->products()->syncWithoutDetaching($product->id);
            $card_details = CardOfProduct::where('product_id',$request->product_id)
                ->where('card_id',$card->id)->first();
            $card_details->quantity = $request->quantity;
            if($card_details->save()){
                DB::commit();
                return $card;
            }
            DB::rollback();
            return false;
        }catch(Exception $ex){
              DB::rollback();
            return false;
        }
    }

    public function clear_cart(){
        $cart = auth()->user()->card;
        if($cart === null){
            $cart = new Card();
            $cart->user_id = auth()->user()->id;
            $cart->save();
        }
        $cart->products()->detach();
        return $this->getMessage('cart has been cleared',200);
    }

    
    /*public function get_total($card){
        $details = CardOfProduct::where('card_id',$card->id)->get();
        $total =0 ;
       
        if(auth()->user()->is_family){
            foreach($details as $product_details){
                $product = Product::find($product_details->product_id);
                $total += $product_details->quantity * $product->family_price;
            }
            return $total;
        }else{
            foreach($details as $product_details){
                $product = Product::find($product_details->product_id);
                $total += $product_details->quantity * $product->staff_price;
            }
            return $total;
        }
    }*/
}
