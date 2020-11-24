
@component('mail::message')
# Hello {{$user->name}}

Use the code below to reset your {{config('app.name')}} password for your {{$user->email  }} account.

Reset Code : {{$user->phone_code}}

If you didn’t ask to reset your password, you can ignore this email.

Thanks,<br>
{{ config('app.name') }}
@endcomponent
