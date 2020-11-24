<?php

namespace App\Transformers\Category;

use App\Category;
use App\Transformers\GeneralTransformer;

class CategoryTransformer extends GeneralTransformer
{

    protected $defaultIncludes = [
        'categories',
    ];
     
    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform(Category $category)
    {
       
        return [
            'id'                => (int)$category->id,
            'title'             => (string)$category->title,
            'image'             => (string)$this->end_point.$category->image,
            'phone_image'       => (string)$this->end_point.$category->phone_image,
        ];
    }

    public function includeCategories(Category $category){
        $categories = $category->categories()->get();    
        return $this->collection($categories,new SubCategoryTransformer);
    }
}
