<?php

namespace App\Http\Controllers\Product;
use App\Brand;
use App\Category;
use App\Company;
use App\Exports\products;
use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use App\Product;
use App\Transformers\Product\Ar\Ar_ProductTransformer;
use App\Transformers\Product\ProductWithVariances;
use App\Transformers\Product\VarianceTransformer;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Exception;
use Illuminate\Support\Facades\DB;


class ProductController extends ApiController
{

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
       /* $products = Product::where('company_name','ALPHA')->get();
       
        $origin_amount = 0;
        $amount = 0;
        foreach($products as $product){
            $origin_amount += $product->stock * $product->price ;
            $amount += $product->stock * $product->price_discount;
        }

        dd($origin_amount." / " .$amount);*/

       /* $products = Product::whereNull('barcode')->get();
        foreach($products as $product){
            $product->barcode = 'sku'. $product->id;
            $product->saveة();
        }*/
        /*$main_category = Category::where('title','fashion')->first();
        $categories = $main_category->categories;
        foreach($categories as $category){
            $products = $category->products()->get();
            foreach($products as $product){
                if($main_category !== null){
                    $main_category->products()->syncWithoutDetaching($product->id);
                }
            }
        }*/
        
        $products = Product::where('company_name','SANDBOX Jewelry')->get();
        
        foreach($products as $product){
            $product->company_name = 'SANDBOX JEWELRY';
            $product->save();
        }
        $products = Product::whereNull('parent_id')->get();
        if($request->language === 'ar'){
            return $this->showAll($products,Ar_ProductTransformer::class);
        }
        else{
            return $this->showAll($products);
        }
    }

    
    public function get_companies(){
        $companies = Company::all();
        return $this->showAll($companies);
    }

    public function getVariances($id){
        $product = Product::findOrFail($id);
        return $this->showOne($product,VarianceTransformer::class);   
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $product = $this->add_product($request);
        if($product){
            return $this->showOne($product);
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
        $product = Product::findOrFail($id);
        if($request->language === 'ar'){
            return $this->showOne($product,Ar_ProductTransformer::class);
        }
        else{
            return $this->showOne($product);
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
        $product = $this->update_product($request,$id);
        if($product){
            return $this->showOne($product);
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
        $product= Product::findOrFail($id);
        if($product->delete()){
            return $this->getMessage($product->title.' has been deleted.',200);
        }
        return $this->getMessage('there is something wrong, try again.',409);
    }


    public function view($id){
        $product = Product::findOrFail($id);
        $product->viewed +=1;
        if($product->save()){
            return $this->getMessage('viewed successfully',200);
        }
        return $this->getMessage('something goes wrong',404);
    }

    public function store_group(Request $request){
       
        $spreadsheet = IOFactory::load($request->file('products'));
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
            'Barcode','Product Name English','Product Name Arabic',
            'Product Information English','Product Information Arabic',
            'How to use EN','How to use AR','Division EN','Brand EN',
            'Category EN','Facebook Page Link','Instagram Page Link',
            'URL'
        ];
        $headers   = array_filter(array_shift($rows));
        $tmp = [];
        
        foreach ($rows as $k => $row) {
            foreach ($headers as $kk => $header) {
                $header = str_replace(' ', '', $header);
                $tmp[$k][$header] = $row[$kk];
            }
        }
        $index = 0 ;

        DB::beginTransaction();
       
        foreach ($tmp as $k => $v) {
            try{
                $product = Product::where('barcode',$v['Barcode'])->first();
                if($product !== null){
                    $product->title = $product->title.' '.$v['Gender'];
                    $product->description = $product->description.' '.$v['Gender'];
                    $product->title_ar = $product->title_ar.' '.$v['Gender'];
                    $product->description_ar = $product->description_ar.' '.$v['Gender'];
                    $product->save();
                }else{
                    continue;
                }
                
               /* $product = new Product();
                $product->image = 'products/'.$v['Barcode'].'.jpg';
                $product->phone_image = 'products/'.$v['Barcode'].'.jpg';
                $product->stock=0;
                if(isset($v['Barcode'])){
                    $product_exist = Product::where('barcode',(string)$v['Barcode'])->get()->first();
                    if($product_exist !== null)
                    {
                        continue;
                    }else{
                        $product->barcode= (string)$v['Barcode'];
                    }
                }
                
                if($v['ProductNameEnglish'] === null){
                    continue;
                }

                if(isset($v['ProductNameEnglish'])){
                    $product->title = ucfirst(strtolower($v['ProductNameEnglish']));
                    $product->description = $v['ProductNameEnglish'];
                    $product->description_ar = $v['ProductNameEnglish'];
                    $product->title_ar =  $product->title;
                }*/
                
            
               /* if(isset($v['ProductNameArabic'])){
                    $product->title_ar = $v['ProductNameArabic'];
                    
                }
    
                if(isset($v['ProductNameArabic'])){
                    if($v['ProductNameArabic'] === 'NA'){
                        if(isset($v['ProductNameEnglish'])){
                            $product->title_ar = $v['ProductNameEnglish'];
                        }
                    }else{
                        $product->title_ar = $v['ProductNameArabic'];
                    }
                }else{
                    if(isset($v['ProductNameEnglish'])){
                        $product->title_ar = $v['ProductNameEnglish']; 
                    }
                }
        
              /*  if(isset($v['ProductInformationEnglish'])){
                    $product->description = $v['ProductInformationEnglish']; 
                }
                if(isset($v['ProductInformationArabic'])){
                    if($v['ProductInformationArabic'] === 'NA'){
                        if(isset($v['ProductInformationEnglish'])){
                            $product->description_ar = $v['ProductInformationEnglish']; 
                            
                        }
                    }else{
                        $product->description_ar = $v['ProductInformationArabic'];
                    }
                }else{
                    if(isset($v['ProductInformationEnglish'])){
                        $product->description_ar = $v['ProductInformationEnglish']; 
                    }
                }*/
               // $brand = Brand::where('title',$v['Brand'])->get()->first();
                // dd($brand);
            /*    if(isset($v['Brand'])){
                    $brand = Brand::where('title',$v['Brand'])->get()->first();
                    if($brand !== null){
                        $product->brand_id = $brand->id;
                    }
                }

                if(isset($v['ItemCode'])){
                   $product->item_code=$v['ItemCode'];
                }
    
                if(isset($v['Company'])){
                    $product->company_name = $v['Company'];
                }
                if(isset($v['Price'])){
                    $product->price = $v['Price'];
                }
             /*  if(isset($v['Color'])){
                    $color = $this->color[strtolower($v['Color'])];
                    $product->color = $color;
                    $parent = Product::where('item_code',$v['ItemCode'])->first();
                    if($parent !== null){
                        $product->parent_id = $parent->id;
                    }
                }

                if(isset($v['Size'])){
                    $product->size = $v['Size'];
                }*/

              /* if(isset($v['Stock'])){
                    $product->stock = $v['Stock'];
                }
    
                if(isset($v['Discount'])){
                    $product->discount = $v['Discount'];
                }
    
                if(isset($v['PriceAfterDiscount'])){
                    $product->price_discount = $v['PriceAfterDiscount'];
                }
                if(isset($v['URL'])){
                    $product->url = $v['URL'];
                }
                //$category = Category::where('title',$v['Category'])->get()->first();
                //dd($category);
                $product->save();
                if(isset($v['Category'])){
                    $category = Category::where('title',$v['Category'])->get()->first();
                    if($category !== null){
                        $category->products()->syncWithoutDetaching($product->id);
                    }
                }
                //$category = Category::where('title',$v['SubCategory'])->get()->first();
                //dd($category);
                if(isset($v['SubCategory'])){
                    $category = Category::where('title',$v['SubCategory'])->get()->first();
                    if($category !== null){
                        $category->products()->syncWithoutDetaching($product->id);
                    }
                }*/
            
            }catch(Exception $ex){
                //dd($v['Barcode']);
                dd($ex);
                DB::rollback();
                continue;
            }
        }
        
        DB::commit();
        return $this->getMessage('Success',200);
    }

    public function update_group(Request $request)
    {
        set_time_limit(0);
        $spreadsheet = IOFactory::load($request->file('products'));
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
            'Barcode','limit_stock'
        ];
        $headers   = array_filter(array_shift($rows));
        $tmp = [];
        foreach ($rows as $k => $row) {
            foreach ($headers as $kk => $header) {
                $header = str_replace(' ', '', $header);
                $tmp[$k][$header] = $row[$kk];
            }
        }
        $index= 1;
        foreach ($tmp as $k => $v) {
            if($v['Barcode'] === null){
                continue;
            }
            $product = Product::where('barcode', (string)$v['Barcode'])->first();
            if($product === null){
                if(  
                 $v['Barcode'] === 3337872412646 
                ||$v['Barcode'] === 3337875486736 
                 || $v['Barcode'] === 3600542081337 || $v['Barcode'] === 3474630491717 ||
                    $v['Barcode'] ===  3474630575479 || $v['Barcode'] ===  3474630634602 || $v['Barcode'] ===3474630709058
                || $v['Barcode'] === 3474632000481 || $v['Barcode'] ===  3474636383108 ||$v['Barcode'] === 3474636616008
                ||  $v['Barcode'] === 3474636834198 ||$v['Barcode'] === 3474636834396 || $v['Barcode'] ===3474636834518 )  {
                    continue;
                }else{
                    dd($v['Barcode']);
                }  
            }
            if(isset($v['limit_stock'])){
                $product->event_stock = $v['limit_stock'];
                $product->save();
            }
            /*dd($product->categories);
            $index++;*/
        }
        dd($index);
        return $this->getMessage('Success',200);
    }

    protected function add_product($request){
        $product = new Product();
        $product->title = (string)$request->title;
        $product->description = (string)$request->description;
        $product->price = (float)$request->price;
        $product->discond = (float)$request->discond;
        $product->staff_price =$request->price -  (($request->price * $request->discond)/100);
        $product->stock = (int)$request->stock;
        $product->SKU = rand(1000,20000);
        $product->image = $request->image->store("products");
        $imageFileNameResize= "products/".md5(date('Y-m-d H:i:s:u')).'.';
        $imageFileNameResize =  $imageFileNameResize . $request->image->getClientOriginalExtension();
        $resize_image="image.".$request->image->getClientOriginalExtension();
        $this->doCreateThumbnail(100,100,url('public/img/'.$product->image),$resize_image);


        Storage::disk('images')
        ->put(
        $imageFileNameResize,
        File::get($resize_image)
        );
        
        $product->phone_image = $imageFileNameResize;
      

        if($product->save()){
            return $product;
        }
        else{
            return false;
        }
        
    }

    protected function update_product($request,$id){

        $product= Product::findOrFail($id);

        if($request->has('title') && $request->get('title') !== null){
            $product->title = $request->title;
        }

        if($request->has('description') && $request->get('description') !== null){
            $product->description = $request->description;
        }

        if($request->has('price') && $request->get('price') !== null){
            $product->price = $request->price;
        }

        if($request->has('stock') && $request->get('stock') !== null){
            $product->stock = $request->stock;
        }

        if($request->has('image')){
            $product->image = $request->image->store("products");
            $imageFileNameResize= "products/".md5(date('Y-m-d H:i:s:u')).'.';
            $imageFileNameResize =  $imageFileNameResize . $request->image->getClientOriginalExtension();
            $resize_image="image.".$request->image->getClientOriginalExtension();
            $this->doCreateThumbnail(100,100,url('public/img/'.$product->image),$resize_image);
            Storage::disk('images')
            ->put(
            $imageFileNameResize,
            File::get($resize_image)
            );
            $product->phone_image = $imageFileNameResize;
        }
        if($product->save()){
            return $product;
        }
        return false;
    }
}
