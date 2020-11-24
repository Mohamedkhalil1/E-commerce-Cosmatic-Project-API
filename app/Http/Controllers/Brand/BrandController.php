<?php

namespace App\Http\Controllers\Brand;

use App\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use App\Http\Controllers\ApiController;

class BrandController extends ApiController
{
    public function __construct()
    {
        //$this->middleware('auth:api');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $brands = Brand::all();
        return $this->showAll($brands);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $brand = $this->add_brand($request);
        if($brand){
            return $this->showOne($brand);
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
        $brand = Brand::findOrFail($id);
        $products = $brand->products()->get();
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
        $brand = $this->update_brand($request,$id);
        if($brand){
            return $this->showOne($brand);
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
        $brand = Brand::findOrFail($id);
        if($brand->delete()){
            return $this->showOne($brand);
        }
        return $this->getMessage('there is something wrong, try again.',409);
    }

    protected function add_brand($request){
        $brand = new Brand();
        $brand->title = $request->title;
        $brand->description = $request->description;
        $brand->image = $request->image->store('brands');
        $brand->division_id= (int)$request->division_id;
        if($brand->save()){
            return $brand;
        }
        return false;
    }

    protected function update_brand($request,$id){

        $brand = Brand::findOrFail($id);

        if($request->has('title') && $request->get('title') !== null){
            $brand->title = $request->title;
        }  

        if($request->has('description') && $request->get('description')!== null ){
            $brand->description = $request->description;
        }

        if($request->has('image')){
            $brand->image = $request->image->store('brands');
        }

        if($brand->save()){
            return $brand;
        }
        return false;
    }
}
