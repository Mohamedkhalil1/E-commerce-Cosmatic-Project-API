<?php

namespace App;

use App\Transformers\Card\CardDetailsTransformer;
use Illuminate\Database\Eloquent\Model;

class CardOfProduct extends Model
{
    protected $fillable = ['product_id','quantity'];
   

    public $transformer = CardDetailsTransformer::class;
}
