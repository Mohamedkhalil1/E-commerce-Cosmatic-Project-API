<?php

namespace App\Transformers\User;

use App\UserAddress;
use League\Fractal\TransformerAbstract;

class UserAddressTransfor extends TransformerAbstract
{
    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected $defaultIncludes = [
        //
    ];
    
    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [
        //
    ];
    
    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform(UserAddress $address)
    {
        return [
            'address' => (string) $address->address,
            'city' => (string) $address->city,
            'district'  => (string)$address->region,
        ];
    }
}
