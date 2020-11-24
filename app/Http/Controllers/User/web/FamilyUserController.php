<?php

namespace App\Http\Controllers\User\web;

use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;

class FamilyUserController extends ApiController
{

    public function __construct()
    {
        $this->middleware('auth:api');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = auth()->user();

        $families = $user->family()->get();
        
        return $this->showAll($families);
    }

    
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
