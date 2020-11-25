<?php

namespace App\Transformers\Brand;

use App\Brand;
use App\Transformers\Category\CategoryTransformer;
use App\Transformers\Category\SubCategoryTransformer;
use App\Transformers\GeneralTransformer;
use Mockery\Matcher\Subset;

class BrandTransformer extends GeneralTransformer
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
            
        ];
    }

    public function includeCategories(Brand $brand){
        $categories = $brand->categories()->whereNotNull('parent_id')->get();
        return $this->collection($categories,new SubCategoryTransformer);
    }
}
