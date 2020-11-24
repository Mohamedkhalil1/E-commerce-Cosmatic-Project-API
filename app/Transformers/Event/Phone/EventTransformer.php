<?php

namespace App\Transformers\Event\Phone;

use App\Event;
use App\Product;
use App\Transformers\Product\ProductTransformer;
use League\Fractal\TransformerAbstract;

class EventTransformer extends TransformerAbstract
{

    protected $defaultIncludes = [
        'products',
    ];

   
    public function transform(Event $event)
    {
        $url = "https://thefamilysale.com/loreal_backend/loreal/public/img/";
        return [
            'id'          =>(int) $event->id,
            'tile'        =>(string) $event->title,
            'body'        => (string)$event->description,
            'image'       => $event->image !== null ?(string) $url.$event->image : null,
        ];
    }

    public function includeProducts(Event $event){
        $products = Product::where('available_for_family',2)->get();
        return $this->collection($products,new ProductTransformer);
    }
}
