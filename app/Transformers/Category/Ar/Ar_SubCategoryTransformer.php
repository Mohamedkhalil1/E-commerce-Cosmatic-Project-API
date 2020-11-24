<?php

namespace App\Transformers\Category\Ar;

use App\Category;
use App\Transformers\GeneralTransformer;


class Ar_SubCategoryTransformer extends GeneralTransformer
{
    public function transform(Category $category)
    {
        return [
            'id'                => (int)$category->id,
            'title'             => (string)$category->title_ar,
            'image'             => (string)$this->end_point.$category->image,
            'phone_image'       => (string)$this->end_point.$category->phone_image,
        ];
    }
}
