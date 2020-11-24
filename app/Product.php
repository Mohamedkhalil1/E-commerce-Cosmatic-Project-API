<?php

namespace App;

use App\Transformers\Product\ProductTransformer;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = ['title', 'description', 'price', 'stock' ,'image' ,'discond'];

    public $transformer = ProductTransformer::class;

    public function orders(){
        return $this->belongsToMany(Order::class,'order_details','product_id','order_id');
    }

    public function categories(){
        return $this->belongsToMany(Category::class,'category_of_products','product_id','category_id');
    }

    public function brand(){
        return $this->belongsTo(Brand::class,'brand_id');
    }

    public function deivisons(){
        return $this->belongsToMany('deivisons','devision_of_products','product_id','deivison_id');
    }

    public function favourites_users(){
        return $this->belongsToMany(User::class,'favourites','product_id','user_id');
    }

    public function ads(){
        return $this->belongsTo(Ad::class,'ad_id');
    }

    public function images(){
        return $this->hasMany(ProductImage::class,'product_id');
    }

    public function products()
    {
        return $this->hasMany(Product::class, 'parent_id');
    }
}
