<?php

namespace App\Http\Controllers\Category;

use App\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;
use App\Transformers\Category\Ar\Ar_CategoryTransformer;

class CategoryController extends ApiController
{
    
    public function index(Request $request)
    {
        $categories = Category::whereNull('parent_id')->get();
        if($request->language === 'ar'){
            return $this->showAll($categories,Ar_CategoryTransformer::class);
        }
        return $this->showAll($categories);
    }

    public function show($id)
    {
        $category = Category::findOrFail($id);
        $products = $category->products()->get();
        return $this->showAll($products);
    }
}
