<?php

namespace App\Transformers\Event\Web;

use App\Event;
use League\Fractal\TransformerAbstract;

class EventTransformer extends TransformerAbstract
{

    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform(Event $event)
    {
        $url = "https://thefamilysale.com/loreal_backend/loreal/public/img/";
        return [
            'id'          =>(int) $event->id,
            'tile'        =>(string) $event->title,
            'description' => (string)$event->description,
            'image'       => $event->image !== null ?(string) $url.$event->image : null,
            'start'       => $event->start,
            'end'         => $event->end
        ];
    }
}
