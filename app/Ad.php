<?php

namespace App;

use App\Transformers\Ad\AdTransformer;
use Illuminate\Database\Eloquent\Model;

class Ad extends Model
{
   protected $fillable = ['title','body','image'];
   protected $table = 'ads';
   public $transformer = AdTransformer::class;

   public function products(){
       return $this->hasMany(Product::class,'ad_id');
   }
} 
