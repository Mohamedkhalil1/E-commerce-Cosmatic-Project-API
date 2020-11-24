<?php

namespace App\Http\Controllers\User\web;

use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;
use App\Mail\ForgetPassword;
use App\User;
use Illuminate\Support\Facades\Mail;

class UserForgetPasswordController extends ApiController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function send_email(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        $user= User::where('email' ,$request->email)->first();
        if($user === null){
            return $this->getMessage('your email is not exist in the website.',404);
        }
        $code = $this->generate_token($user->id);
        $user->code = $code ;
        $user->save();
        Mail::to($user)->send(new ForgetPassword($user));
        if (Mail::failures()) {
            dd(Mail::failures());
           }
        return $this->getMessage('Your request has been send to email' ,200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function new_password(Request $request)
    {
        $request->validate([
            'token' => 'required|string',
            'password' => 'required|confirmed|min:6',
        ]);

        $user= User::where('code',$request->token)->first();
        if($user === null){
            return $this->getMessage('your token or email is not exist in the website.',404);
        }

        $user->password = bcrypt($request->password);
        $user->code = null;
        $user->save();
        return $this->getMessage('your password has been changed successfully.');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function resend_email(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        $user= User::where('email' ,$request->email)->first();
        if($user === null){
            return $this->getMessage('your email is not exist in the website.',404);
        }
        $code = rand(1000,9999);
        $user->code = $this->generate_token($user->id) ;
        $user->save();
        Mail::to($user)->send(new ForgetPassword($user));
        return $this->getMessage('Your Code has been send to email again' ,200);
    }
}
