<?php

namespace App\Transformers\Product;

use App\Product;
use App\Transformers\GeneralTransformer;

class VarianceTransformer extends GeneralTransformer
{
    public function transform(Product $product)
    {
        $variances= null;
       // if($product->company_name === 'COTTONIL' || $product->company_name === 'Gamy Sports'){
            if($product->parent_id === null && $product->products()->first() === null){
                $variances= null;
            }else{
                $variances = $this->getVariances($product);
            }
           
       // }
        
        return[
            'variances' => $variances
        ];
    }


    private function getVariances($product){
      
        if($product->parent_id !== null){
           
            $product = Product::find($product->parent_id);
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
            if($product->color === $color){
                
                $products = Product::where('parent_id',$product->id)->where('color',$color)->orWhere('id',$product->id)->select('id','size','image','phone_image','color','stock','price','price_discount')->get();
            }else{
                $products = Product::where('parent_id',$product->id)->where('color',$color)->select('id','size','image','phone_image','color','stock','price','price_discount')->get();
            }
          
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
