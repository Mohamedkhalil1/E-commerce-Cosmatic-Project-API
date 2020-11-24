<?php

namespace App;

use App\Transformers\Devision\DevisionTransformer;
use Illuminate\Database\Eloquent\Model;

class Deivison extends Model
{
    protected $fillable = ['title', 'description'];

    public $transformer = DevisionTransformer::class;

    public function brands(){
        return $this->hasMany(Brand::class,'division_id');
    }
}
