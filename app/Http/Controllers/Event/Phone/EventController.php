<?php

namespace App\Http\Controllers\Event\Phone;

use App\Event;
use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;
use App\Transformers\Event\Phone\EventTransformer;
use App\User;

class EventController extends ApiController
{

    public function __construct()
    {
      //  $this->middleware('auth:api');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        /*$users = User::where('pocket_money',0)->get();

        foreach($users as $user){
            $user->pocket_money=10000;
            $user->save();
        }
        return $this->getMessage('Success',200);*/

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
        $event = Event::findOrFail($id);
        return $this->showOne($event,EventTransformer::class);
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
