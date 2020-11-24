<?php

namespace App\Transformers\Product;

use App\Product;
use App\Traits\ApiResponser;
use App\Transformers\GeneralTransformer;
use App\Transformers\Product\VarianceTransformer;
use League\Fractal\TransformerAbstract;

class ProductWithVariances extends GeneralTransformer
{
    use ApiResponser;

    public function transform(Product $product)
    {
        $user= auth()->user();
        $is_favoruite=0;
        $in_cart=0;
        if($user){
            $is_favoruite= $this->is_favoruite($product,$user);  
            $in_cart = $this->in_cart($product,$user);
        }
       
        $variances = $this->getVariances($product);

        return [
            'id'                              => (int)$product->id,
            'title'                           => (string)$product->title,
            'description'                     => (string)$product->description,  
            'price'                           => $product->price,
            'stock'                           => (int)$product->stock,    
            'image'                           => $product->image ? $this->end_point.(string)$product->image : '',
            'phone_image'                     => $product->phone_image ? $this->end_point.(string)$product->phone_image : '',
            'discount'                        => $product->discount.'%',
            'price_discount'                  => $product->price_discount,   
            'is_favoruite'                    => (bool)$is_favoruite,
            'in_cart'                         => (bool)$in_cart,
            'company'                         => $product->company_name,
            'brand_id'                        => (int)$product->brand_id,
            'brand'                           => $product->brand ?  (string) $product->brand->title : '',
            'url'                             => $product->url,
            'variance'                        => $variances
          ];
    }

    public static function originAttribute ($index){
        $attrubites = [
            'id'                              => 'id',
            'title'                           => 'title',
            'description'                     => 'description',     
            'price'                           => 'price_discount',  
            'stock'                           => 'stock',  
            'image'                           => 'image',  
            'discond'                         => 'discond',  
            'brand'                           => 'brand_id',
            'ad'                              => 'ad_id',
            'company'                         => 'company_name'
        ];
        return isset($attrubites[$index]) ? $attrubites[$index] : null ;
    }      
    
    private function is_favoruite($product , $user){
        return $user->favourites()->find($product->id) ? true : false;
    }

    private function in_cart($product , $user){
        $cart = $user->card;
        if($cart){
            return $cart->products()->find($product->id) ? true : false; 
        }
        return false;  
    }

    public function includeImages(Product $product){
        $images = $product->images()->get();
        return $this->collection($images,new ProductImageTransformer);
    }

    private function getVariances($product){
       
        if($product->parent_id !== null){
           
            $product = Product::find($product->id);
        }
        $variances = $product->products()->get();
        $colors=array();
        array_push($colors,$product->color);
        foreach($variances as $variance){
            array_push($colors,$variance->color);
        }
        $colors=array_unique($colors);
        $array = array();
        $sizes_array=array();
        foreach($colors as $color){
            $products = Product::where('parent_id',$product->id)->where('color',$color)->orWhere([['id',$product->id,'color',$product->color]])->select('id','size','image','phone_image','color','stock','price','price_discount')->get();
            array_push($array,$products);
        } 
        
        
        foreach($array as $products){
           foreach($products as $product){
               $product->image = $this->end_point.$product->image;
               $product->phone_image = $this->end_point.$product->phone_image;
           }
        }
        return $array;
    }
}
