<?php

namespace App;

use App\Transformers\UserTransformer;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use Notifiable;

    public $transformer = UserTransformer::class;

    protected $fillable = ['name', 'email', 'password','phone' ,'remember_token' ,'avatar'];


    public function orders(){
        return $this->hasMany(Order::class,'user_id');
    }

    public function card(){
        return $this->hasOne(Card::class,'user_id');
    }

    public function getIsFamilyAttribute(){
        return $this->isFamily();
    }
    public function isFamily()
    {
        return $this->parent_id !== null || (int)$this->is_staff === 0;
    }
    
    public function favourites(){
        return $this->belongsToMany(Product::class,'favourites','user_id','product_id');
    }

    public function staff()
    {
        return $this->belongsTo(User::class, 'parent_id');
    }

    public function family()
    {
        return $this->hasMany(User::class, 'parent_id');
    }

    public function userAddress(){
        return $this->hasOne(UserAddress::class,'user_id');
    }

    public function contact_us(){
        return $this->hasMany(ContactUs::class,'user_id');
    }

     // Rest omitted for brevity

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

}
