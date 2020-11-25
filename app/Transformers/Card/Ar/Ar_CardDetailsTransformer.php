<?php

namespace App\Transformers\Card\Ar;

use App\CardOfProduct;
use App\Product;
use App\Transformers\GeneralTransformer;

class Ar_CardDetailsTransformer extends GeneralTransformer
{
     
    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform(CardOfProduct $details)
    {
        $product = Product::find($details->product_id);

        return [
            'id'             => (int)$details->id,
            'product_id'     => (int)$product->id,
            'title'          => (string)$product->title_ar,
            'stock'          => (int)$product->stock ,
            'image'          => $this->end_point.(string)$product->image,
            'quantity'       => (int)$details->quantity,
            'price'          => $product->price,
            'price_discount' => $product->price_discount,
            'amount'         => $product->price_discount * $details->quantity
        ];
    }

}
