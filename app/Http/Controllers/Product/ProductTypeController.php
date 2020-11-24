<?php

namespace App\Http\Controllers\Product;

use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;
use App\Product;

class ProductTypeController extends ApiController
{

    public function __construct()
    {
      //  $this->middleware('auth:api');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if($request->has('type')&& $request->get('type')){
            if($request->type === 'popluar'){
                $products = Product::orderBy('viewed','desc')->limit(8)->get();
            }
            elseif($request->type="top_sellers"){
                $products = Product::orderBy('count_selled','desc')->limit(8)->get();
            }
            else{
                $products = Product::orderBy('created_at','desc')->limit(8)->get();
            }
            return $this->showAll($products);
        }
        return $this->getMessage("something goes wrong, please try again",404);
    }
}
