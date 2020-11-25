<?php

namespace App\Transformers\Devision\Ar;

use App\Deivison;
use App\Transformers\Brand\Ar\Ar_BrandTransformer;
use League\Fractal\TransformerAbstract;

class Ar_DevisionTransformer extends TransformerAbstract
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
            'title'             => (string)$devision->title_ar,
        ];
    }

    public function includeBrands(Deivison $devision){
        $brands = $devision->brands()->get();
        return $this->collection($brands,new Ar_BrandTransformer);
    }
}
