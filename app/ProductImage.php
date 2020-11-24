<?php

namespace App;

use App\Transformers\Product\ProductImageTransformer;
use Illuminate\Database\Eloquent\Model;

class ProductImage extends Model
{
    protected $fillable = ['image','product_id'];
    public $transformer = ProductImageTransformer::class;
    public function product(){
        return $this->belongsTo(Product::class,'product_id');
    }
}
