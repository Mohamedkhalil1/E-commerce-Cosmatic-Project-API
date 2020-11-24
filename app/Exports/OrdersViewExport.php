<?php

namespace App\Exports;

use App\Order;
use App\OrderDetails;
use App\Product;
use Carbon\Carbon;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
class OrdersViewExport implements FromView  
{
    public function view(): View
    {
        /*$now = Carbon::now();
        $start = $now->startOfWeek(Carbon::SATURDAY)->format('Y-m-d');
        $end = $now->endOfWeek(Carbon::FRIDAY)->format('Y-m-d');
        
        return view('exports.orders', [
            'orders' => Order::where('created_at','>=',$start)
            ->where('created_at','<=',$end)->orderBy('user_id','asc')->get()
        ]);*/
     //  $products = Product::whereIn('id',[8960,8970,8992,8996,9007,9024,9037,9076,9079,9085,9092,9102,9132,9134,9168,9175,9244,9316,9342,9385,9402,9461,9466,9549]);
        // $products = Product::whereIn('id',[2428,2430,2435,2439,2442,2444,2448,2451,2465,2466,2468,2469,2473,2475,2476,2477,2478,2479,2668,2681,2450,2453,2456,2459,2460,2644,2645,2649,2650,2651,2653,2655,2659,2664])->get();4

        $orders = Order::where('done',1)->get();
        $products = array();
        foreach($orders as $order){
            $order_products = $order->products()->where('company_name','BELITA COSMETICS')->get();
            foreach($order_products as $product){
                $details = OrderDetails::where('product_id',$product->id)->where('order_id',$order->id)->first();
                $array = [
                    'barcode' => $product->barcode,
                    'title'   => $product->title,
                    'quantity' => $details->quantity,
                    'price'    => $product->price,
                    'price_discount' => $product->price_discount
                ];
                //dd($array);
                array_push($products,$array);
            }
        }
       
        return view('exports.products', [
            'products' =>$products
            ]);
    }
}
