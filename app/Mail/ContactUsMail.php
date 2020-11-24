<?php

namespace App\Mail;

use App\ContactUs;
use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class ContactUsMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $contact_us;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(User $user , ContactUs $contact_us )
    {
        $this->user = $user;   
        $this->contact_us = $contact_us;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('emails.contact_us')->subject('Message');
    }
}
