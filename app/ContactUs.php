<?php

namespace App;

use App\Transformers\User\ContactUsTransformer;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContactUs extends Model
{
    public $transformer = ContactUsTransformer::class;

    protected $fillable = ['subject', 'message', 'user_id'];

    public function user(){
        return BelongsTo(User::class,'user_id');
    }

}
