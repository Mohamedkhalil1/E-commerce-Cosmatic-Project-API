<?php

namespace App\Http\Controllers\Ad;

use App\Ad;
use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;


class AdController extends ApiController
{
    public function index(Request $request)
    {
        try{
            $ads = Ad::all();
            return $this->showAll($ads); 
        }catch(\Exception $ex){
            return $this->getMessage(__('ads.error'),404);
        }
    }


    public function show($id,Request $request)
    {
        try{
            $ad = Ad::findOrFail($id);
            return $this->showOne($ad);
        }catch(\Exception $ex){
            return $this->getMessage(__('ads.error'),404);
        }
    } 
}
