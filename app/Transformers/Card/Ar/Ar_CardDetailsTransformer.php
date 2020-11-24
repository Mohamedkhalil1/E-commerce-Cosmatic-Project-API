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
            'image'          => $this->end_point.(string)$product->phone_image,
            'quantity'       => (int)$details->quantity,
            'brand'          => $product->brand ?  (string) $product->brand->title : '',
            'price'          => $product->price,
            'price_discount' => $product->price_discount,
            'amount'         => $product->price_discount * $details->quantity
        ];
    }

}
