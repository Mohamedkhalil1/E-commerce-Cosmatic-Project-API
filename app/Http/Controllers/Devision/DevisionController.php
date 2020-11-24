<?php

namespace App\Http\Controllers\Devision;

use App\Deivison;
use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;
use App\Transformers\Devision\Ar\Ar_DevisionTransformer;

class DevisionController extends ApiController
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
    public function index(Request $request)
    {
        if($request->language === 'ar'){
            $devisions = Deivison::all();
            return $this->showAll($devisions,Ar_DevisionTransformer::class);
        }
        $devisions = Deivison::all();
        return $this->showAll($devisions);
      
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $devision = $this->add_devision($request);
        if($devision){
            return $this->showOne($devision);
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
        $devision = Deivison::findOrFail($id);
        return $this->showOne($devision);
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
        $category = $this->update_devision($request,$id);
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
        $devision = Deivison::findOrFail($id);
        if($devision->delete()){
            return $this->showOne($devision);
        }
        return $this->getMessage('there is something wrong, try again.',409);
    }

    protected function add_devision($request){

        $devision = new Deivison();

        $devision->title = $request->title;
        $devision->description = $request->description;

        if($devision->save()){
            return $devision;
        }
        return false;
    }

    protected function update_devision($request,$id){

        $devision = Deivison::findOrFail($id);

        if($request->has('title') && $request->get('title') !== null){
            $devision->title = $request->title;
        }  

        if($request->has('description') && $request->get('description')){
            $devision->description = $request->description;
        }
        if($devision->save()){
            return $devision;
        }
        return false;
    }
}
