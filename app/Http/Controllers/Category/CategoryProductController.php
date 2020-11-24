<?php

namespace App\Http\Controllers\Category;

use App\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;
use App\Product;

class CategoryProductController extends ApiController
{
    public function __construct()
    {
      //  $this->middleware('auth:api');
    }

    public function index(Request $request){
        $ids = explode(',',$request->category);
        $products = Product::whereHas('categories', function($q) use($ids) {
            $q->whereIn('id', $ids);
        })->get();
        return $this->showAll($products);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $category_id , $product_id)
    {
        $category = Category::findOrFail($category_id);
        $category->products()->syncWithoutDetaching($product_id);

        return $this->showOne($category);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($category_id,$product_id)
    {
        $category = Category::findOrFail($category_id);

        $category->products()->detach($product_id);

        return $this->showOne($category);
    }
}
