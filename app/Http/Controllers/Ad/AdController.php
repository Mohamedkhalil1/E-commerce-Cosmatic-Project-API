<?php

namespace App\Http\Controllers\Ad;

use App\Ad;
use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;
use App\Product;
use App\Transformers\Ad\AdFamilyTransformer;
use App\Transformers\Ad\Ar\Ar_AdFamilyTransformer;
use App\Transformers\Ad\Ar\Ar_AdTransformer;
use PhpOffice\PhpSpreadsheet\IOFactory;

class AdController extends ApiController
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
    public function index(Request $request)
    {

        if($request->language==='ar'){
            $ads = Ad::all();
            return $this->showAll($ads,Ar_AdTransformer::class);
        }else{
            $ads = Ad::all();
            return $this->showAll($ads);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $ad = $this->add_ad_excel($request);
        if($ad){
            return $this->showOne($ad);
        }
        return $this->getMessage('there is something wrong, try again.',409);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id,Request $request)
    {
        if($request->language==='ar'){
            $ad = Ad::findOrFail($id);
            return $this->showOne($ad,Ar_AdTransformer::class);
        }else{
            return $this->showOne($ad);
        }
   
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
        $ad = Ad::findOrFail($id);
        $ad = $this->update_ad($request,$ad);
        if($ad){
            return $this->showOne($ad);
        }
        return $this->getMessage('something goes wrong, please try again',404);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $ad = Ad::findOrFail($id);
        if($ad->delete()){
            return $this->getMessage('ad has been delete',200);
        }
        return $this->getMessage('something goes wrong, please try again',404);
    }

    protected function add_ad($request){
        $ad = new Ad();
        $ad->title = (string)$request->title;
        $ad->body = (string)$request->body;
        $ad->image = $request->image->store("products/ads");
        $ad->phone_image = $request->phone_image->store("products/ads/small");
        $ad->is_family = (int)$request->is_family;
        $ad->save();    
        return $ad;
    }

    public function update_ad($request , $ad){

        if($request->has('title') && $request->get('title')){
            $ad->title = (string)$request->title;
        }

        if($request->has('body') && $request->get('body')){
            $ad->body = (string)$request->body;
        }

        if($request->has('image')){
            if((string)$ad->image !== ''){
                $image_path = public_path().'/img/'.$ad->image;
                unlink($image_path);
            }
            $ad->image = $request->image->store('products/ads');
        }
        if(!$ad->save()){
            return false;
        }
        
        if($request->has('is_family') && $request->get('is_family')){
            $ad->is_family = (int)$request->is_family;
        }

        if($request->has('products') && $request->get('products')){
            $products = $ad->products;
            foreach($products as $product){
                $product->ad_id = null;
                $product->save();
            }

            $products = explode(',',$request->products);
            foreach($products as $product_id)
            $product = Product::findOrFail($product_id);
            $product->ad_id = $ad->id;
            $product->save();
        }
        return $ad;
    }

    public function add_ad_excel($request){

        $ad = new Ad();
        $ad->title = (string)$request->title;
        $ad->body = (string)$request->body;
        $ad->image = $request->image->store("products/ads");
        $ad->phone_image = $request->phone_image->store("products/ads/small");
        $ad->is_family = (int)$request->is_family;
        $ad->save();

       /* $spreadsheet = IOFactory::load($request->file('products'));
        $worksheet   = $spreadsheet->getActiveSheet();
        $rows        = [];
        foreach ($worksheet->getRowIterator() AS $row) {
            $cellIterator = $row->getCellIterator();
            $cellIterator->setIterateOnlyExistingCells(FALSE);
            $cells = [];
            foreach ($cellIterator as $cell) {
                $cells[] = $cell->getValue();
            }
            $rows[] = $cells;
        }
        
        $titles    = [
            'Produt','SKU'
        ];
        $headers   = array_filter(array_shift($rows));
        $tmp = [];
        foreach ($rows as $k => $row) {
            foreach ($headers as $kk => $header) {
                $header = str_replace(' ', '', $header);
                $tmp[$k][$header] = $row[$kk];
            }
        }

        foreach ($tmp as $k => $v) {
            if($v['SKU'] === null){
                continue;
            }
            if(isset($v['SKU'])){
                $product = Product::where('SKU', $v['SKU'])->first();
                if($product === null){
                    continue;
                }
                $product->ad_id = $ad->id;
                $product->save();
            }
        }*/
        return $ad;
    }

}
