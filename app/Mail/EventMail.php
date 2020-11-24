<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class EventMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $event;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($user,$event)
    {
        $this->user= $user;
        $this->event = $event;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('event.event')->subject('Event Advertisement');
    }
}
