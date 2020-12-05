<?php 

namespace App\Traits;
use App\Brand;
use App\Category;
use App\Product;
use App\Transformers\Product\Ar\Ar_ProductTransformer;
use App\Transformers\Product\ProductTransformer;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

trait ApiResponser{
    public function successResponse($data ,$code){
        return response()->json($data , $code);
    }

    protected function errorResponse($message,$code){
        return response()->json(['message' => $message , 'code' => $code] , $code);
    }

    protected function showAll(Collection $collection ,$transformer='',$lang='en',$code=200){
        if($collection->isEmpty()){
                return $this->successResponse(['data' => $collection],$code);
            }
        if($transformer === ''){
            $transformer = $collection->first()->transformer;
        }
        
        if($transformer === ProductTransformer::class || $transformer === Ar_ProductTransformer::class ){
            $user = auth()->user();
            $collection = $this->typeProduct($collection,$transformer,$lang);
            $collection = $this->price($collection,$transformer,$user,$lang);
            $collection = $this->filterData($collection,$transformer,$lang);
            $collection = $this->sortData($collection,$transformer,$lang);
            $collection = $this->search($collection,$lang);
            $collection = $this->paginate($collection);
            $collection = $this->cacheResponse($collection);
        }else{
            $collection = $this->paginate($collection);
            $collection = $this->cacheResponse($collection);
        }
        $collection = $this->transformData($collection,$transformer);
        return $this->successResponse($collection, $code);
    }

    public function showArray($array,$code=200){
        $data = array();
        if(!count($array)){
            return $this->successResponse(['data' => $array],$code);
        }
        foreach($array as $collection){
            $transformer = $collection->first()->transformer;
            $collection = $this->transformData($collection,$transformer);
            array_push($data, $collection);
        }
        return $this->successResponse((object)$data, $code);
    }

    protected function showOne(Model $instance ,$transformer='', $code=200){
       if($transformer === ''){
        $transformer = $instance->first()->transformer;
        }
       $instance = $this->transformData($instance,$transformer);
       return $this->successResponse($instance,$code);
    } 

    protected function getMessage($message,$code=200){
        return $this->successResponse(['message' => $message],$code);
    }

    protected function transformData($data,$transformer)
    {   
        $transformation = fractal($data,new $transformer);
        return $transformation->toArray();
    }

    private function filterData(Collection $collection , $transformer){
        $category = false;
        foreach(request()->query() as $query=>$value){
            if($query === 'category'){
                $titles = explode(',',$value);

                if(request()->has('search_price')){
                  
                    $attributes = explode(',',request()->get('search_price'));
                    if(count($attributes) === 2){
                        $collection =  Product::whereNull('parent_id')->whereHas('categories', function($q) use($titles) {
                            $q->whereIn('title', $titles)->orWhereIn('title_ar',$titles);
                            })
                            ->where('price_discount','>=',(int)$attributes[0])
                            ->where('price_discount','<=',(int)$attributes[1])
                            ->get();

                        /*$collection = $collection->where('price_discount','>=',(int)$attributes[0])
                        ->where('price_discount','<=',(int)$attributes[1]);*/
                    }

                }else{
                    $collection = Product::whereNull('parent_id')->whereHas('categories', function($q) use($titles) {
                        $q->whereIn('title', $titles)->orWhereIn('title_ar',$titles);
                    })
                    ->get();
                }
            }
        }

        foreach(request()->query() as $query=>$value){
            if($query === 'category'){
                continue;
            }
            $attribute = $transformer::originAttribute($query);
            if(isset($attribute,$value)){
                $collection = $collection->where($attribute,$value);
            }
        }
        return $collection;
    }

    private function search($collection){

        if(request()->has('search')){
            $brands= Brand::where('title','LIKE','%'.request()->get('search').'%')->get();
            $brand_ids= array();
            foreach($brands as $brand){
                array_push($brand_ids,$brand->id);
            }
            $categories= Category::where('title','LIKE','%'.request()->get('search').'%')
                ->orWhere('title_ar','LIKE','%'.request()->get('search').'%')->get();
            $category_ids= array();
            foreach($categories as $category){
                array_push($category_ids,$category->id);
            }
            
            $collection = Product::whereNull('parent_id')->whereHas('categories',function ($q) use ($category_ids) {
                $q->whereIn('categories.id', $category_ids);
            })
            ->orWhere('title','LIKE','%'.request()->get('search').'%')
            ->orWhereIn('brand_id',$brand_ids)->get();
            
        }
       
        return $collection;
    }
    private function searchCategory(Collection $collection , $transformer){
        foreach(request()->query() as $query=>$value){
            if($query === "category"){
               $ids = explode(',',$value);
               $collection = DB::table('products')
               ->join('category_of_products', 'category_of_products.product_id', '=', 'products.id')
               ->join('categories', 'categories.id', '=', 'category_of_products.category_id')
               ->where(function($q) use($ids)
               {
                   $q->where('categories.id',$ids);
               })->get();
            }
        }
        return $collection;
    }

    private function sortData(Collection $collection,$transformer)
    {
        if(request()->has('sort_by')){
            $attributes = explode(',',request()->get('sort_by'));
            if(count($attributes) === 2){
                if((int)$attributes[1] === 1){
                    $attribute = $transformer::originAttribute($attributes[0]);
                    $collection = $collection->sortByDesc->{$attribute};
                }else{
                    $attribute = $transformer::originAttribute($attributes[0]);
                    $collection = $collection->sortBy->{$attribute};
                }
            }
        }
        return $collection;
    }

    private function price(Collection $collection,$transformer,$user){
        if(request()->has('search_price')){
            $attributes = explode(',',request()->get('search_price'));
            if(count($attributes) === 2){
                $collection = Product::whereNull('parent_id')->where('price_discount','>=',$attributes[0])
                                    ->where('price_discount','<=',$attributes[1])->orderBy('price_discount','asc')->get();
            }   
        }
        return $collection;
    }

    public function typeProduct(Collection $collection , $transformer){
        if(request()->has('type')){
            if(request()->get('type') === 'popular'){
                $collection = Product::whereNull('parent_id')->orderBy('viewed','desc')->get();
            }
            elseif(request()->get('type') === 'best-seller'){
                $collection = Product::whereNull('parent_id')->orderBy('count_selled','asc')->get();
            }
            elseif(request()->get('type') === 'last-pieces'){
                $collection = Product::whereNull('parent_id')->orderBy('stock','desc')->get();
            }else if(request()->get('type') === 'new-arrival'){
                $collection = Product::whereNull('parent_id')->orderBy('created_at','desc')->get();
            }
        }
        return $collection;
    }

    private function paginate(Collection $collection){

        $rules = [
            'per_page' => 'integer|min:2|max:50',
        ];
        request()->validate($rules);
        $perPage =10;

        if(request()->has('per_page')){
            $perPage = (int) request()->per_page;
        }
        $page = LengthAwarePaginator::resolveCurrentPage();
        
        $result = $collection->slice(($page-1)*$perPage,$perPage)->values();
        $paginated= new  LengthAwarePaginator($result,$collection->count(),$perPage,$page,[
            'path' => LengthAwarePaginator::resolveCurrentPath()
        ]);
            // for another attribute as sort_by , filter 
        $paginated->appends(request()->all());    
        return $paginated;
    }

    private function cacheResponse($data){
        $url = request()->url();
        
        return Cache::remember($url, 30/60, function()use($data){
            return $data;
        });
    }

    public function returnValidationError($validator)
    {
        return $this->getMessage($validator->errors()->first(),404);
    }
}

?>