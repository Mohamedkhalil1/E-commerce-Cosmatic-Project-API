<?php

namespace App\Http\Controllers\Devision;

use App\Deivison;
use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;
use App\Transformers\Devision\Ar\Ar_DevisionTransformer;

class DevisionController extends ApiController
{
    public function index(Request $request)
    {
        try{
            if($request->language === 'ar'){
                $divisions = Deivison::all();
                return $this->showAll($divisions,Ar_DevisionTransformer::class);
            }
            $divisions = Deivison::all();
            return $this->showAll($divisions);
        }catch(\Exception $ex){
            return $this->getMessage(__('divisions.error'));
        }
    }

    public function show($id)
    {
        try{
            $devision = Deivison::findOrFail($id);
            return $this->showOne($devision);
        }catch(\Exception $ex){
            return $this->getMessage(__('divisions.error'));
        }
    }
}
