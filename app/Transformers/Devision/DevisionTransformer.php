<?php

namespace App\Transformers\Devision;

use App\Deivison;
use App\Transformers\Brand\BrandTransformer;
use App\Transformers\Product\ProductTransformer;
use League\Fractal\TransformerAbstract;

class DevisionTransformer extends TransformerAbstract
{
    protected $defaultIncludes = [
        'brands',
    ];
    
    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform(Deivison $devision)
    {
        return [
            'id'                => (int)$devision->id,
            'title'             => (string)$devision->title,
        ];
    }

    public function includeBrands(Deivison $devision){
        $brands = $devision->brands()->get();    
        return $this->collection($brands,new BrandTransformer);
    }
}
