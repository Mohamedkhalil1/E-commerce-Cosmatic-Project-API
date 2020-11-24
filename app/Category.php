<?php

namespace App;

use App\Transformers\Category\CategoryTransformer;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = ['title', 'description'];

    public $transformer = CategoryTransformer::class;

    public function products(){
        return $this->belongsToMany(Product::class,'category_of_products','category_id','product_id');
    }

    public function brands(){
        return $this->belongsToMany(Category::class,'category_brands','category_id','brand_id');
    }

    public function categories(){
        return $this->hasMany(Category::class,'parent_id');
    }


}
