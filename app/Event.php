<?php

namespace App;

use App\Transformers\Event\Web\EventTransformer;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $fillable = ['title', 'description','start','image','end'];
    public $transformer = EventTransformer::class;

    public function products(){
        return $this->hasMany(Product::class,'event_id');
    }

}
