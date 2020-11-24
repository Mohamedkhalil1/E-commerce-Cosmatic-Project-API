<?php

namespace App\Http\Controllers\Order;

use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;
use App\Order;
use App\Services\R2SService;
use App\Transformers\Order\Admin\AllOrderTransformer;
use App\User;

class OrderController extends ApiController
{

    public function __construct()
    {
        //$this->middleware('auth:api');
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if($request->has('city') && $request->get('city') !== null){
            $orders = Order::where('city',$request->city)->orderBy('created_at','desc')->get();
        }
        elseif($request->has('staff') && $request->get('staff')){
            $orders = Order::where('user_id',$request->staff)->orderBy('created_at','desc')->get();
        }

       /* elseif($request->has('family') && $request->get('family') !== null){
            $user_ids = array();
            $user = User::findOrFail($request->family);
            array_push($user_ids , $user->id);
            $family = $user->family;
            foreach($family as $mem){
                array_push($user_ids,$mem->id);
            }
            $orders = Order::whereIn('user_id',$user_ids)->orderBy('created_at','desc')->get();
        }
        else{
            $orders = Order::orderBy('created_at','desc')->get();
        }
        */
        
        $orders = Order::orderBy('invoice_num','asc')->where('done',1)->whereNull('waybill')->get();
        $amount = 0;
        foreach($orders as $order){
            if($order->payment_type === 'card'){
                $amount = 0;
            }else{
                $amount = $order->amount;
            }

            if($order->payment_type === 'credit on delivery'){
                $this->handleR2S($order->user,$order,'DROPDCC',$amount,'Credit Card');
            }else{
                $this->handleR2S($order->user,$order,'DROPD',$amount,'Cash');
            }
        } 
        
        return $this->showAll($orders,AllOrderTransformer::class);

    }

    private function handleR2S($user,$order,$pud,$amount,$payment_mode){
         $r2s = new R2SService();
         $response = $r2s->createWaybill($user,$order,$pud,$amount,$payment_mode);
        
         $order->waybill=$response->waybillNumber;
         $order->invoice_url  = $response->labelURL;
         $order->save();
         return true;
     }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
