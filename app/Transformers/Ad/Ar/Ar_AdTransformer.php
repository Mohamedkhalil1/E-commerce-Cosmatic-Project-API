<?php

namespace App\Transformers\Ad\Ar;

use App\Ad;
use App\Transformers\Product\Ar\Ar_ProductTransformer;
use League\Fractal\TransformerAbstract;

class Ar_AdTransformer extends TransformerAbstract
{
    protected $defaultIncludes = [
        'products',
    ];

    public function transform(Ad $ad)
    {
        $url = "https://familysale.s3.eu-west-2.amazonaws.com/";
        return [
            'id'                => (int)$ad->id,
            'title'             => (string)$ad->title,
            'body'              => (string)$ad->body,
            'image'             => $ad->image === null ? '' : (string)$this->end_point.$ad->image,
            'phone_image'       => $ad->phone_image !== null ? (string)$this->end_point.$ad->phone_image : null
        ];
    }

    public function includeProducts(Ad $ad){
        $products = $ad->products()->get();
        return $this->collection($products,new Ar_ProductTransformer);
    }
}
