<?php

namespace App\Http\Controllers\Devision;

use App\Deivison;
use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;

class DevisionProductController extends ApiController
{
    public function __construct()
    {
      //  $this->middleware('auth:api');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $devision_id , $product_id)
    {
        $devision = Deivison::findOrFail($devision_id);
        $devision->products()->syncWithoutDetaching($product_id);

        return $this->showOne($devision);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($devision_id,$product_id)
    {
        $devision = Deivison::findOrFail($devision_id);

        $devision->products()->detach($product_id);

        return $this->showOne($devision);
    }
}
