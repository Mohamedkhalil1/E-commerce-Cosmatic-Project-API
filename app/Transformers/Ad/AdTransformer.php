<?php

namespace App\Transformers\Ad;

use App\Ad;
use App\Traits\ApiResponser;
use App\Transformers\GeneralTransformer;
use App\Transformers\Product\ProductTransformer;
use League\Fractal\TransformerAbstract;

class AdTransformer extends GeneralTransformer
{

    protected $defaultIncludes = [
        'products',
    ];

    public function transform(Ad $ad)
    {
      
        return [
            'id'                => (int)$ad->id,
            'title'             => (string)$ad->title,
            'image'             => $ad->image === null ? '' : (string)$this->end_point.$ad->image,
        ];
    }

    public function includeProducts(Ad $ad){
        $products = $ad->products()->get();
        return $this->collection($products,new ProductTransformer);
    }
    
}
