<?php

namespace App\Http\Controllers\User;

use App\ContactUs;
use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;

class UserContactUsController extends ApiController
{
    public function store(Request $request)
    {
        try{
            $contact_us = new ContactUs();
            if(auth()->user() === null){
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
            }else{
                $request->validate([
                    'subject' => 'required|string|max:200',
                    'message' => 'required|string|max:2000'
                ]);
                $contact_us->phone = $request->phone;
                $contact_us->message = $request->message;
                $contact_us->subject = $request->subject;
                $contact_us->email = $request->email;
                $contact_us->user_id = auth()->user()->id;
                
            }
            $contact_us->save();
            return $this->getMessage(__('contacts.thank_you'),200);
        }catch(\Exception $ex){
            return $this->getMessage(__('contacts.error'),404);
        }
        
    }

}
