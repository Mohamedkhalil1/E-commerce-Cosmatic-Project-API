<?php

namespace App\Http\Controllers\User\Phone;

use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;
use App\Mail\Phone\ForgetPassword;
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
        $code = rand(1000,9999);
        $user->phone_code = $code ;
        $user->save();
        Mail::to($user)->send(new ForgetPassword($user));
        return $this->getMessage('Your Code has been send to email' ,200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function match_code(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'code' => 'required|integer'
        ]);
        $user= User::where('email' ,$request->email)->first();
        if($user === null){
            return $this->getMessage('your email is not exist in the website.',404);
        }
        if((int)$request->code !== (int)$user->phone_code){
            return $this->getMessage('your code invalid.',404);
        }else{
            return $this->getMessage('your code is correct',200);
        }
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
            'email' => 'required|email',
            'password' => 'required|confirmed|min:6',
        ]);

        $user= User::where('email' ,$request->email)->first();
        if($user === null){
            return $this->getMessage('your email is not exist in the website.',404);
        }
        $user->password = bcrypt($request->password);
        $user->phone_code=null;
        $user->save();
        return $this->getMessage('your password has been changed',200);
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
        $user->phone_code = $code ;
        $user->save();
        Mail::to($user)->send(new ForgetPassword($user));
        return $this->getMessage('Your Code has been send to email again' ,200);
    }   
}
