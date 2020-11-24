<?php

namespace App\Http\Controllers\Event\Web;

use App\Event;
use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;
use App\Mail\EventMail;
use App\Product;
use App\Transformers\Event\Web\EventTransformer;
use App\User;
use Illuminate\Support\Facades\Mail;

class EventController extends ApiController
{

    public function __construct()
    {
       // $this->middleware('auth:api');
    }

    public function index(){
        $event = Event::where('active',1)->first();
        if($event === null){
            return $this->getMessage('there is not event ',404);
        }
        return $this->showOne($event,EventTransformer::class);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $event = $this->add_event($request);
        if($event){
            $users = User::all();
            foreach($users as $user){
                if((int)$user->is_new === 0){
                    $user->pocket_money = 1000;  
                }else{
                    $user->pocket_money = 6000;
                }
               
                $user->save();
            }
            return $this->getMessage('event has been created',201);
        }
        return $this->getMessage('something goes wrong ',404);
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
    
    public function set_image($id,Request $request){
        $event = Event::findOrFail($id);

        $event->image = $request->image->store('products/event');
        if($event->save()){
            return $this->getMessage('event image has been update.',200);
        }
        return $this->getMessage('something goes wrong',404);

    }

    public function activate($id){
        $event = Event::findOrFail($id);
        $event->active =1 ;
        $event->save();

        $users= User::where('parent_id',null)->get();
        foreach($users as $user){
            Mail::to($user)->send(new EventMail($user,$event));
        }

        return $this->getMessage('event has been started',201);
    }

    private function add_event($request){
        $event = new Event();

        $event->title = $request['title'];
        $event->description = $request['description'];
        $startTime   = strtotime($request['start']);
        $event->start = date("d-m-Y",$startTime);

        $endTime   = strtotime($request['end']);
        $event->end = date("d-m-Y",$endTime);

        $event->save();
       // $event->image = $request->image->store('events');
       $products = $request['products']['data'];
       foreach($products as $product){
           $origin_product = Product::findOrFail($product['product_id']);
           $origin_product->event_stock = $product['stock'];
           $origin_product->event_id = $event->id;
           $origin_product->family_discond = $product['discond'];
           $origin_product->family_price =$origin_product->price - (($origin_product->price * $product['discond'])/100);
           $origin_product->available_for_family = 1;
           $origin_product->save();
       }
        return true;
    }
}
