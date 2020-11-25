<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;
use App\Mail\UserCreated;
use App\User;
use App\UserAddress;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserProfileController extends ApiController
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function update_data(Request $request){

        try{
            DB::beginTransaction();
            $user = auth()->user();
            if($request->has('name')&& $request->get('name') !== null){
                $user->name = $request->name;  
            }
            if($request->has('phone') && $request->get('phone') !== null){
                $rules = [
                    "phone" => "required|numeric",
                ];
        
                $validator = Validator::make($request->all(),$rules);
                if ($validator->fails()) {
                    return $this->returnValidationError($validator);
                }
                $user->phone = $request->phone;
            }
            $user->save();
            $address = $user->userAddress === null ? new UserAddress() : $user->userAddress;
            if($request->has('address') && $request->get('address') !== null){
                $address->user_id = $user->id;
                $address->address = $request->address;
                $address->save();
            }
            if($request->has('city') && $request->get('city') !== null){
                $address->user_id = $user->id;
                $address->city = $request->city;
                $address->save();
            }
            if($request->has('district') && $request->get('district') !== null){
                $address->user_id = $user->id;
                $address->region = $request->district;
                $address->save();
            }
            DB::commit();
            return $this->getMessage('your data has been updated',200);
        }catch(Exception $ex){
            return $this->getMessage('error in data',404);
        }
       
    } 

    public function change_password(Request $request){
        $user=auth()->user();
        if(Hash::check($request->old_password,$user->password)){
            $request->validate([
                'new_password' => 'required|confirmed|min:7',
                'email'        => 'nullable|email',
                'phone'        => 'nullable|string'
            ]);
            if($request->has('email') && $request->get('email') !== null){
                if($request->email !== $user->email){
                    $check_email = User::where('email',$request->email)->first(); 

                    if($check_email !== null){
                        return $this->getMessage('Email already exists, Try another email or leave it blank!',402);
                    }
                    else{
                        $user->email = $request->email;
                    }
                }
            }
            if($request->has('phone')&&$request->get('phone')!== null){
                $user->phone = $request->phone;
            }
            
            $user->password = $request->new_password;
            if((int)$user->first_time === 1){
                $user->first_time = 0;
             }
            //$user->save();
           // Mail::to($user)->send(new UserCreated($user));
            $user->password = bcrypt($user->password);
            $user->save();
            return $this->getMessage('password has been changed correctly',200);

        }else{
            return $this->getMessage('Old password is incorrect, Chek it again!',406);
        }
    }

}
