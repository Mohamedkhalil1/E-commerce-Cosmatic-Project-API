
@component('mail::message')
# Hello {{$user->name}}

Use the code below to reset your Egypt Outlet Online password for your {{$user->email  }} account.

@component('mail::button', ['url' => "https://thefamilysale.com/#/reset-password/{$user->code}"])
    Reset Password
@endcomponent

If you didn’t ask to reset your password, you can ignore this email.

Thanks,<br>
    Egypt Outlet Online 
@endcomponent
