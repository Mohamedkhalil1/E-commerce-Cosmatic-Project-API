<?php

namespace App\Exports;

use App\Order;
use App\OrderDetails;
use App\User;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class FamilyOrders implements FromView
{
    private $user;

    public function __construct(User $user)
    {
      $this->user= $user;
    }

    public function view(): View
    {
        $array_ids = array();
        $family = $this->user->family;
       /* array_push($array_ids,$this->user->id);
        foreach($family as $mem){
            array_push($array_ids,$mem->id);
        }
        $orders = Order::whereIn('user_id', $array_ids)->get();
        $array_orders= array();
        $amount=0;
        foreach($orders as $order){
            $amount += $order->amount;
            array_push($array_orders,$order->id);
        }
       
        $details = OrderDetails::whereIn('order_id',$array_orders)->get();*/
        return view('exports.families', [
            'users' => $family,
            'staff'=> $this->user
        ]);
    }
}
