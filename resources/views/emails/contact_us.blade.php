@component('mail::message')

    # Subject : {{$contact_us->subject}}
    ------------------------------------------
    # Message : {{$contact_us->message}}

    From : {{$user->email}}
 
@endcomponent
