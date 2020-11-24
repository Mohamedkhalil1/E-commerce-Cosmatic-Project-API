<?php

namespace App\Transformers\Order\Admin;

use App\Order;
use App\OrderDetails;
use App\Transformers\Order\OrderDetailsTransformer;
use League\Fractal\TransformerAbstract;

class AllOrderTransformer extends TransformerAbstract
{
    /*protected $defaultIncludes = [
        'details',
    ];*/

    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform(Order $order)
    {
        return [
            'id'          => (int)$order->id,
            'amount'      => (float) $order->amount,
            'customer'    => (string) $order->user ? $order->user->name : '',
            'city'        => (string) $order->city,
            'status'      => (int) $order->done === 0 ? 'Processing' : 'Done',
            'number'      => $order->num,
            'main_image'  => $order->products()->first()->image,
            'main_image_phone'  => $order->products()->first()->phone_image,
            'shipping_fees'   =>    $order->shipping_fees,
            'date'        => $order->created_at->format('M d, Y'),
            'waybill'     => $order->waybill
        ];
    }

   /* public function includeDetails(Order $order){
        $details = OrderDetails::where('order_id',$order->id)->get();
        return $this->collection($details,new OrderDetailsTransformer());
    }*/
}
