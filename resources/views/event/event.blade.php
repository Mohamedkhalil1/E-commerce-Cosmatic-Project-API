
@component('mail::message')
# Hello {{$user->name}}

{{$event->title}} has been started

@component('mail::button', ['url' => "http://trainingroiapp.com/trainingroiapp.com/loreal/event"])
    Visit
@endcomponent


Thanks,<br>
{{ config('app.name') }}
@endcomponent
