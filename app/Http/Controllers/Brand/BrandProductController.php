<?php

namespace App\Http\Controllers\Brand;

use App\Brand;
use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;
use App\Product;

class BrandProductController extends ApiController
{
    public function __construct()
    {
        //$this->middleware('auth:api');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $brand_id , $product_id)
    {
        $brand = Brand::findOrFail($brand_id);
        //$brand->products()->syncWithoutDetaching($product_id);
        $product = Product::findOrFail($product_id);
        $product->brand_id = $brand_id;
        $product->save();
        $products = $brand->products()->get();
        return $this->showAll($products);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($brand_id,$product_id)
    {
        $brand = Brand::findOrFail($brand_id);
        $product = Product::findOrFail($product_id);
        $product->brand_id =null;
        $product->save();
        $products = $brand->products()->get();
        return $this->showAll($products);
    }
}
