<?php

namespace App\Http\Controllers\Category;

use App\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;
use App\Transformers\Category\Ar\Ar_CategoryTransformer;

class CategoryController extends ApiController
{

    public function __construct()
    {
       // $this->middleware('auth:api');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $categories = Category::whereNull('parent_id')->get();
        if($request->language === 'ar'){
            return $this->showAll($categories,Ar_CategoryTransformer::class);
        }
        return $this->showAll($categories);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $category = $this->add_category($request);
        if($category){
            return $this->showOne($category);
        }
        return $this->getMessage('there is something wrong, try again.',409);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $category = Category::findOrFail($id);
        $products = $category->products()->get();
        return $this->showAll($products);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $category = $this->update_category($request,$id);
        if($category){
            return $this->showOne($category);
        }
        return $this->getMessage('there is something wrong, try again.',409);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $category = Category::findOrFail($id);
        if($category->delete()){
            return $this->showOne($category);
        }
        return $this->getMessage('there is something wrong, try again.',409);
    }

    protected function add_category($request){

        $category = new Category();

        $category->title = $request->title;
        $category->description = $request->description;

        if($category->save()){
            return $category;
        }
        return false;
    }

    protected function update_category($request,$id){

        $category = Category::findOrFail($id);

        if($request->has('title') && $request->get('title') !== null){
            $category->title = $request->title;
        }  

        if($request->has('description') && $request->get('description')){
            $category->description = $request->description;
        }

        if($request->has('image')){
            $category->image = $request->image->store('products/categories');
        }
        
        if($category->save()){
            return $category;
        }
        return false;
    }
}
