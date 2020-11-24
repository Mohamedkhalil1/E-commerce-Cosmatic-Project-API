<?php

namespace App\Transformers\User;

use App\ContactUs;
use League\Fractal\TransformerAbstract;

class ContactUsTransformer extends TransformerAbstract
{
    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected $defaultIncludes = [
        //
    ];
    
    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [
        //
    ];
    
    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform(ContactUs $contactUs)
    {
        return [
            'subject' => $contactUs->subject,
            'message' => $contactUs->message,
            'user_name' => $contactUs->user->name
        ];
    }
}
