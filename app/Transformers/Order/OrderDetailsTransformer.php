<?php

namespace App\Transformers\Order;

use App\OrderDetails;
use App\Product;
use App\Transformers\GeneralTransformer;

class OrderDetailsTransformer extends GeneralTransformer
{
    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected $defaultIncludes = [
        //
    ];
    
    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [
        //
    ];
    
    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform(OrderDetails $order_details)
    {
        $user= auth()->user();
        $product = Product::find($order_details->product_id);
        if($product !== null){
            $is_favoruite= $this->is_favoruite($product,$user);  
        }
        else{
            $is_favoruite=false;
        }

        return [
            'id'               => (int) $order_details->id,
            'order_id'         => (int) $order_details->order_id,
            'product_id'       => Product::find($order_details->product_id) ? (int)Product::find($order_details->product_id)->id : '' ,
            'product_title'    => Product::find($order_details->product_id) ? (string)Product::find($order_details->product_id)->title : '' ,
            'product_image'    => Product::find($order_details->product_id) ? $this->end_point.Product::find($order_details->product_id)->image : '' ,
            'brand'            => Product::find($order_details->product_id) ? (string)Product::find($order_details->product_id)->brand ?  (string) Product::find($order_details->product_id)->brand->title : '' : '',
            'price'            => $order_details->price,
            'price_discount'   => $order_details->price_discount,
            'quantity'         => (int) $order_details->quantity,
            'is_favoruite'     => (bool)$is_favoruite,
            'shipping_fees'    => (int) $order_details->shipping_fees,
            'discount'         => (double) $order_details->discond,
        ];
    }

    private function is_favoruite($product , $user){
        return $user->favourites()->find($product->id) ? true : false;
    }
}
