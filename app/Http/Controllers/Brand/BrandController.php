<?php

namespace App\Http\Controllers\Brand;

use App\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use App\Http\Controllers\ApiController;
use Exception;

class BrandController extends ApiController
{
    public function index()
    {
        try{
            $brands = Brand::all();
            return $this->showAll($brands);
        }catch(Exception $ex){
            return $this->getMessage(__('brands.error'));
        }
        
    }

    public function show($id)
    {
        try{
            $brand = Brand::findOrFail($id);
            $products = $brand->products()->get();
            return $this->showAll($products);
        }catch(Exception $ex){
            return $this->getMessage(__('brands.error'));
        }
    }
}
