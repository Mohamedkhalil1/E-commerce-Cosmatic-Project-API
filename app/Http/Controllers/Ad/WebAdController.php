<?php

namespace App\Http\Controllers\Ad;

use App\Ad;
use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;
use App\Transformers\Ad\WebAdTransformer;
use App\Transformers\Product\FamilyProductTransformer;
use App\Transformers\Product\ProductTransformer;

class WebAdController extends ApiController
{

    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
      //  $this->middleware('auth:api');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $ads = Ad::all();
        $transformer = WebAdTransformer::class;
        return $this->showAll($ads,$transformer);
    }
    public function show($id)
    {
        $ad = Ad::findOrFail($id);
        $products = $ad->products()->get();
        return $this->showAll($products);
    }
}
