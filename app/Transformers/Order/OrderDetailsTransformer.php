<?php

namespace App\Transformers\Order;

use App\OrderDetails;
use App\Product;
use App\Transformers\GeneralTransformer;

class OrderDetailsTransformer extends GeneralTransformer
{
   
    
    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform(OrderDetails $order_details)
    {
        $product = Product::find($order_details->product_id);
        return [
            'id'               => (int) $order_details->id,
            'order_id'         => (int) $order_details->order_id,
            'product_id'       => $product->id ,
            'product_title'    => $product->title,
            'product_image'    => $product->image ,
            'price'            => $order_details->price,
            'price_discount'   => $order_details->price_discount,
            'quantity'         => (int) $order_details->quantity
        ];
    }

  
}
