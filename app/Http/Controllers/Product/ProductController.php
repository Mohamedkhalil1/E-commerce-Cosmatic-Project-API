<?php

namespace App\Http\Controllers\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;
use App\Product;
use App\Transformers\Product\Ar\Ar_ProductTransformer;
use App\Transformers\Product\VarianceTransformer;


class ProductController extends ApiController
{
    public function index(Request $request)
    {
        try{
            $products = Product::whereNull('parent_id')->get();
            if($request->language === 'ar'){
                return $this->showAll($products,Ar_ProductTransformer::class);
            }
            else{
                return $this->showAll($products);
            }
        }catch(\Exception $ex){
            return $this->getMessage(__('products.error'),404);
        }
    }

    public function getVariances($id){
        try{
            $product = Product::findOrFail($id);
            return $this->showOne($product,VarianceTransformer::class); 
        }catch(\Exception $ex){
            return $this->getMessage(__('products.error'),404);
        }
    }

  
  
    public function show($id,Request $request)
    {
        try{
            $product = Product::findOrFail($id);
            if($request->language === 'ar'){
                return $this->showOne($product,Ar_ProductTransformer::class);
            }
            else{
                return $this->showOne($product);
            }    
        }catch(\Exception $ex){
            return $this->getMessage(__('products.error'),404);
        }
       
    }

    
    public function view($id){
        try{
            $product = Product::findOrFail($id);
            $product->viewed +=1;
            if($product->save()){
                return $this->getMessage(__('products.viewed'),200);
            }
            return $this->getMessage(__('products.error'),404);
        }catch(\Exception $ex){
            return $this->getMessage(__('products.error'),404);
        }
       
    }
}
