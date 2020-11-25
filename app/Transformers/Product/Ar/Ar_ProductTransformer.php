<?php

namespace App\Transformers\Product\Ar;

use App\Product;
use App\Transformers\GeneralTransformer;
use App\Transformers\Product\ProductImageTransformer;

class Ar_ProductTransformer extends GeneralTransformer
{

    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform(Product $product)
    {
      
        $user= auth()->user();
        $is_favoruite=0;
        $in_cart=0;
        if($user){
            $is_favoruite= $this->is_favoruite($product,$user);  
            $in_cart = $this->in_cart($product,$user);
        }
        //$discond = $this->get_discond($product);
        return [
            'id'                              => (int)$product->id,
            'title'                           => (string)$product->title_ar,
            'description'                     => (string)$product->description_ar,  
            'price'                           => $product->price,
            'stock'                           => (int)$product->stock,    
            'image'                           => $product->image ? $this->end_point.(string)$product->image : '',
            'price_discount'                  => $product->price_discount,   
            'is_favoruite'                    => (bool)$is_favoruite,
            'in_cart'                         => (bool)$in_cart,
            'brand_id'                        => (int)$product->brand_id,
            'brand'                           => (string)$product->brand->title,
            'category'                        => $product->categories()->WhereNull('parent_id')->first()->title_ar,
            'sub_category'                    => $product->categories()->WhereNotNull('parent_id')->first()->title_ar,
          ];
    }

    public static function originAttribute ($index){
        $attrubites = [
            'id'                              => 'id',
            'title'                           => 'title_ar',
            'description'                     => 'description_ar',     
            'price'                           => 'price_discount',  
            'stock'                           => 'stock',  
            'image'                           => 'image',  
            'discond'                         => 'discond',  
            'brand'                           => 'brand_id',
            'ad'                              => 'ad_id',
            'creation_date'                   => 'created_at',
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

    /*private function get_discond($product){

        if($product->available_for_family){
            return $product->family_price;
        }
        return $product->staff_price;
    }*/


    public function includeImages(Product $product){
        $images = $product->images()->get();
        return $this->collection($images,new ProductImageTransformer);
    }
}
