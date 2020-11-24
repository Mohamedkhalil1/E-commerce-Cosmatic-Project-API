<?php

namespace App\Transformers\Order;

use App\Order;
use App\OrderDetails;
use App\Transformers\GeneralTransformer;

class webOrderTransformer extends GeneralTransformer
{
    protected $defaultIncludes = [
        'images',
    ];

    
    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform(Order $order)
    {
        if((int)$order->done === 0){
            $status = "processing";
        }elseif((int)$order->done === 1){
            $status = "done";
        }elseif((int)$order->done === 2){
            $status = "rejected";
        }else{
            $status = "pending";
        }
        return [
            'id'          => (int)$order->id,
            'system_reference' => $order->system_reference,
            'invoice_num' => $order->invoice_num,
            'amount'      => (float) $order->amount,
            'customer'    => (string) $order->user ? $order->user->name : '',
            'status'      => $status,
            'number'      => $order->invoice_num,
            'shipping_fees'   =>  $order->shipping_fees,
            'main_image'  => $this->end_point.$order->products()->first()->image,
            'tracing_status' => $order->tracing_status,
            'tracing_date' => $order->tracing_date,
            'date'        => $order->created_at->format('M d, Y')
        ];
    }

    public function includeImages(Order $order){
        $details = OrderDetails::where('order_id',$order->id)->limit(4)->get();
        return $this->collection($details,new webOrderDetailsTransformer());
    }
}
