<?php

namespace App\Transformers\Category;

use App\Category;
use App\Transformers\GeneralTransformer;


class SubCategoryTransformer extends GeneralTransformer
{
    public function transform(Category $category)
    {
        return [
            'id'                => (int)$category->id,
            'title'             => (string)$category->title,
            'image'             => (string)$this->end_point.$category->image
        ];
    }
}
