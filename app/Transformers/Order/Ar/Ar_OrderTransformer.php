<?php

namespace App\Transformers\Order\Ar;

use App\Order;
use App\OrderDetails;
use App\Transformers\GeneralTransformer;

class Ar_OrderTransformer extends GeneralTransformer
{
    protected $defaultIncludes = [
        'details',
    ];

    public function transform(Order $order)
    {
        if((int)$order->done === 0){
            $status = "pending";
        }elseif((int)$order->done === 1){
            $status = "paid";
        }
        return [
            'id'          => (int)$order->id,
            'invoice_num' => $order->invoice_num,
            'amount'      => (float) $order->amount,
            'customer'    => (string) $order->user ? $order->user->name : '',
            'status'      => $status,
            'number'      => $order->num,
            'main_image'  => $this->end_point.$order->products()->first()->image,
            'shipping_fees'   =>    $order->shipping_fees,
            'date'        => $order->created_at->format('M d, Y')
        ];
    }

    public function includeDetails(Order $order){
        $details = OrderDetails::where('order_id',$order->id)->get();
        return $this->collection($details,new Ar_OrderDetailsTransformer());
    }
}
