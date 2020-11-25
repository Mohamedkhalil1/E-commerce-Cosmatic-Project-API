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
        return [
            'id' => (int) $user->id,
            'name' => (string) $user->name,
            'email' => (string) $user->email,
            'phone' =>  $user->phone,
            'government' => (string) $user->government,
            'city' => (string) $user->city,
            'address' => (string) $user->address,
            'avatar' => (string)$user->avatar,
            'notification' => (int) $user->notification,
           
        ];
    }
}
