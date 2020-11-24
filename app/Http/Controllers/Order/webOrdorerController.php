<?php

namespace App\Http\Controllers\Order;

use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;
use App\Transformers\Order\webOrderTransformer;

class webOrdorerController extends ApiController
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
        $user= auth()->user();
        $orders = $user->orders()->get();
        return $this->showAll($orders,webOrderTransformer::class);
    }
}
