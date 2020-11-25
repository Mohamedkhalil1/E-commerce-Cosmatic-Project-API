<?php

namespace App\Http\Controllers\User;

use App\Card;
use App\CardOfProduct;
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
        $cart = $user->card;
       
        if($cart === null){
            $cart = new Card();
            $cart->user_id = $user->id;
            $cart->save();
        }
        $cart->changed = 0;
        $cart->save();

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
            return $this->showOne($cart,Ar_CardTransformer::class);
        }else{
            return $this->showOne($cart);
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
        $cart = $user->card;
        if($cart === null){
            $cart = new Card();
            $cart->user_id = $user->id;
            $cart->save();
        }
        $product = Product::findOrFail($request->product_id);
        if($product->stock < $request->quantity){
            return $this->getMessage(__('cart.out_of_stock'),402);
        }
        $card = $this->add_product_card($request,$cart);
        if($card){
            return $this->showOne($card);
        }
        return $this->getMessage(__('cart.not_found'),402);
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
        return $this->getMessage(__('cart.product_removed'),200);
    }
    
    protected function add_product_card($request,$cart){
        try{
            DB::beginTransaction();
            $product = Product::findOrFail($request->product_id);
            $cart->products()->syncWithoutDetaching($product->id);
            $cart_details = CardOfProduct::where('product_id',$request->product_id)
                ->where('card_id',$cart->id)->first();
            $cart_details->quantity = $request->quantity;
            $cart_details->save();
            DB::commit();
            return $cart;
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
        return $this->getMessage(__('cart.clear'),200);
    }
}
