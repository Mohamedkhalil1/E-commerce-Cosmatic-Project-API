<?php

namespace App\Exports;

use App\Event;
use App\Order;
use App\OrderDetails;
use App\Product;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class EventOrders implements FromView
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function view() : view
    {
        //$event = Event::orderBy('id','desc')->first();
       /* $orders =  Order::where('created_at','>=',$event->start)
        ->where('created_at','<=',$event->end)->orderBy('user_id','asc')->get();*/

        /*$orders = Order::where('amount','>','0')->where('created_at','>','2020-06-25')
            ->where('created_at','<','2020-06-26')->get();*/

        $orders = Order::orderBy('invoice_num','asc')->where('done',1)->get();

        $array_orders= array();
        $amount = 0;
       /*$orders = Order::where('amount','>','0')
            ->orderBy('user_id','asc')->get();*/
        foreach($orders as $order){
            $amount += $order->amount;
            array_push($array_orders,$order->id);
        }
        
        $details = OrderDetails::whereIn('order_id',$array_orders)->get();
        return view('exports.orders2', [
            'orders' => $orders,
            'details'=> $details,
            'amount' => $amount
        ]);
    }
}
