<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;


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
            $params = $request->only('name','phone','address','city','government','api_token','notification');
            $user->update($params);
            DB::commit();
            return $this->getMessage('your data has been updated',200);
        }catch(Exception $ex){
            DB::rollback();
            return $this->getMessage('error in data',404);
        }
       
    } 

    public function change_password(Request $request){
        try{
            $user=auth()->user();
            if(Hash::check($request->old_password,$user->password)){
                $request->validate([
                    'new_password' => 'required|confirmed|min:7',
                ]);
                $user->password = bcrypt($request->new_password);
                $user->save();
                return $this->getMessage(__('users.password_changed'),200);

            }else{
                return $this->getMessage(__('users.old_password_not_correct'),406);
            }
        }catch(\Exception $ex){
            return $this->getMessage(__('users.error'),404);
        }
    }

}
