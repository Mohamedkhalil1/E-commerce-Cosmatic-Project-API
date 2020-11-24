<?php

namespace App;

use App\Transformers\User\UserAddressTransfor;
use Illuminate\Database\Eloquent\Model;

class UserAddress extends Model
{
    
    public $transformer = UserAddressTransfor::class;

    protected $fillable = ['address', 'city', 'region', 'user_id'];

    public function user(){
        return $this->belongsTo(User::class,'user_id');
    }

}
