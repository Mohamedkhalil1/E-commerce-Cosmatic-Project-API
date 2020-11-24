<?php

namespace App\Transformers\Product;

use App\Product;
use App\Transformers\GeneralTransformer;

class ProductTransformer extends GeneralTransformer
{
    protected $defaultIncludes = [
        'images',
    ];
    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [
        'meta',
    ];
    
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
       
       // $product->categories()->WhereNotNull('parent_id')->first() === null ? : $product->categories()->WhereNotNull('parent_id')->first()->title,
       $category = ''; 
       if($product->categories()->WhereNotNull('parent_id')->first() === null ){
            if( $product->categories()->WhereNull('parent_id')->first() !== null){
                $category= $product->categories()->WhereNull('parent_id')->first()->title;
            }
        }else{
            $category = $product->categories()->WhereNotNull('parent_id')->first()->title;
        }
        //$discond = $this->get_discond($product);
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
            'category'                        => $category ,//$product->categories->first() === null ? '' : $product->categories->first()->title ,
            'color'                           => $product->color,
            'size'                           => $product->size,
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

    
}
