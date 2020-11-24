<?php

namespace App\Http\Controllers\User\Phone;

use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;

class UserNotificationController extends ApiController
{

    public function __construct()
    {
        $this->middleware('auth:api');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'notification' => 'nullable|numeric'
        ]);
        if($request->has('notification') && $request->get('notification') !== null) 
        $user->notification = (int)$request->notification;
        $user->save();
        if((int)$request->notification === 0){    
            return $this->getMessage('notification is off' , 200);
        }
        return $this->getMessage('notification is on' , 200);
    }
}
