<?php

namespace App\Transformers;

use App\Traits\ApiResponser;
use App\Transformers\User\UserAddressTransfor;
use App\User;
use App\UserAddress;
use League\Fractal\TransformerAbstract;

class UserTransformer extends TransformerAbstract
{
    use ApiResponser;
    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform(User $user)
    {
        $url = "http://trainingroiapp.com/trainingroiapp.com/khalil/public/img/";

        if($user->userAddress === null){
            return [
                'id' => (int) $user->id,
                'name' => (string) $user->name,
                'email' => (string) $user->email,
                'avatar' => (string)$user->avatar !== '' ? $url.$user->avatar : '',
                'notification' => (int) $user->notification,
                'phone'        =>  $user->phone,
            ];
        }else{
           $shipping_address = $this->transformData($user->userAddress,UserAddressTransfor::class);
            return [
                'id' => (int) $user->id,
                'name' => (string) $user->name,
                'email' => (string) $user->email,
                'avatar' => (string)$user->avatar !== '' ? $url.$user->avatar : '',
                'shipping_address'  => $shipping_address,
                'notification' => (int) $user->notification,
                'phone'        =>  $user->phone,
            ];
        }
        
    }
}
