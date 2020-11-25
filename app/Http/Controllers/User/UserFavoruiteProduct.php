<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;
use App\Product;
use App\Transformers\Product\Ar\Ar_ProductTransformer;
use App\Transformers\Product\ProductTransformer;
use Exception;

class UserFavoruiteProduct extends ApiController
{
    
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function index(Request $request)
    {
        try{
            $user = auth()->user();
            $favoruites = $user->favourites()->get();
            if($request->language==='ar'){
                return $this->showAll($favoruites,Ar_ProductTransformer::class);
            }else{
                return $this->showAll($favoruites,ProductTransformer::class);
            }
        }catch (Exception $ex){
            return $this->getMessage(__('products.favoruite_error'),404);
        }
    }


    public function store(Request $request)
    {
        try{
            $user= auth()->user();
            Product::findOrFail($request->product_id);
            $user->favourites()->syncWithoutDetaching($request->product_id);
            return $this->getMessage(__('products.favoruite_added'),200);
        }catch(Exception $ex){
            return $this->getMessage(__('products.favoruite_error'),404); 
        }
       
    }

    public function destroy($id)
    {
        try{
            $user= auth()->user();
            $product = Product::findOrFail($id);
            $user->favourites()->detach($id);
            return $this->getMessage(__('products.favoruite_removed'),200);
        }catch(Exception $ex){
            return $this->getMessage(__('products.favoruite_error'),404); 
        }
       
     
    }

    public function clear_favourite(){
        try{
            auth()->user()->favourites()->detach();
            return $this->getMessage(__('products.favourite_clear'),200);
        }catch(Exception $ex){
            return $this->getMessage(__('products.favoruite_error'),404); 
        }
    }
}
