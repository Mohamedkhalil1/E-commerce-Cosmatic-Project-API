<?php

namespace App\Transformers\Brand\Ar;

use App\Brand;
use App\Transformers\Category\Ar\Ar_CategoryTransformer;
use League\Fractal\TransformerAbstract;

class Ar_BrandTransformer extends TransformerAbstract
{
    protected $defaultIncludes = [
        'categories',
    ];
    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform(Brand $brand)
    {
        return [
            'id'                => (int)$brand->id,
            'title'             => (string)$brand->title,
            'image'             => $brand->image !== null ? $this->end_point.$brand->image : null,
            'phone_image'       => $brand->phone_image !== null ? $this->end_point.$brand->phone_image : null
        ];
    }

    public function includeCategories(Brand $brand){
        $categories = $brand->categories()->get();
        return $this->collection($categories,new Ar_CategoryTransformer);
    }
}
