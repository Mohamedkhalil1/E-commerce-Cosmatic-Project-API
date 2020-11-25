<?php

namespace App\Http\Controllers\User;

use App\ContactUs;
use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;

class UserContactUsController extends ApiController
{

    public function __construct()
    {
       // $this->middleware('auth:api');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        set_time_limit(0);
        $contact_us = new ContactUs();
        $request->validate([
            'subject' => 'required|string|max:200',
            'message' => 'required|string|max:2000',
            'phone'    => 'required|numeric',
            'email'    => 'required'
        ]);
        $contact_us->phone = $request->phone;
        $contact_us->message = $request->message;
        $contact_us->subject = $request->subject;
        $contact_us->email = $request->email;
        $contact_us->user_id = auth()->user() === null ? null : auth()->user()->id;
        $contact_us->save();
       /* $admin_user= User::where('email','Familysale.loe@loreal.com')->first();
        if($admin_user !== null){
            Mail::to($admin_user)->send(new ContactUsMail(auth()->user(),$contact_us));
        }*/

        return $this->getMessage('thanks for message',200);
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
