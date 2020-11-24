<?php

namespace App\Transformers\Ad;

use App\Ad;
use League\Fractal\TransformerAbstract;

class WebAdTransformer extends TransformerAbstract
{
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
}
