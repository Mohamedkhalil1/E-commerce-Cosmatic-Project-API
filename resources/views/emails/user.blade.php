@component('mail::message')
    # Dear {{$user->name}}

    Welcome to The Family Sale. The WOW Solution Company is honored to invite to the upcoming Family Sale. Don't miss the great discounts offered. Come online and enjoy the hassle free experience. 
    # Your username: {{$user->email}} Your Password: {{$user->password}}

    # Visit our Website : thefamilysale.com
    # Your family and friends logins

    -------------------------------------------------
    @foreach ($user->family as $i => $member)
        Family member.{{++$i}}
    -------------------------------------------------
        Email: {{$member->email}}
        Password: {{'family@'.--$i.$user->id}}
    -------------------------------------------------
    @endforeach

    N.B. Make sure to read the FAQ as rules will be strictly applied & Terms and conditions as the responsibility will be on the user to accept it.
    Thanks
    The WOW Solution
 
@endcomponent
