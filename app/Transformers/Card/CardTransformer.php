<?php

namespace App\Transformers\Card;

use App\Card;
use App\CardOfProduct;
use App\Product;
use App\Shipping;
use App\Transformers\Product\ProductTransformer;
use League\Fractal\TransformerAbstract;

class CardTransformer extends TransformerAbstract
{
    protected $defaultIncludes = [
        'details',
    ];
    
    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform(Card $card)
    {
        $total = $this->get_total($card);
        $origin_total = $this->get_origin_total($card);
        $shipping_fess = $this->get_shipping_fess();
        return [
            'id'        => (int)$card->id,
            'customer'  => $card->user ? $card->user->name : '',
            'total_price'     =>$total,
            'total_origin_price' => $origin_total,
            'shipping_fees' => $shipping_fess,
            'changed'       => (bool)$card->changed
        ];
    }

    public function includeDetails(Card $card){
        $details = CardOfProduct::where('card_id',$card->id)->get();
        return $this->collection($details,new CardDetailsTransformer);
    }

    public function get_total($card){
        $details = CardOfProduct::where('card_id',$card->id)->get();
        $total =0 ;
        foreach($details as $product_details){
            $product = Product::find($product_details->product_id);
            $total += $product_details->quantity * $product->price_discount;
        }
        return $total;
    }

    public function get_origin_total($card){
        $details = CardOfProduct::where('card_id',$card->id)->get();
        $total =0 ;
        foreach($details as $product_details){
            $product = Product::find($product_details->product_id);
            $total += $product_details->quantity * $product->price;
        }
        return $total; 
    }

    private function get_shipping_fess(){
        $user= auth()->user();
        if($user->city === null){
            $shipping = 50;
        }else{
            $shipping = Shipping::where('city',auth()->user()->city)->first();
            if($shipping===null){
                $shipping = 50;
            }else{
                $shipping = $shipping->shipping;
            }
        }
        return $shipping;
    }

}
