<?php

namespace App\Transformers\Order;

use App\OrderDetails;
use App\Product;
use App\Transformers\GeneralTransformer;

class webOrderDetailsTransformer extends GeneralTransformer
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
        return [
            'image'    => Product::find($order_details->product_id) ? $this->end_point.Product::find($order_details->product_id)->image : '' ,
        ];
    }
}
