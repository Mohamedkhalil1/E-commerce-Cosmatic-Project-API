@component('mail::message')
# Order 

  {{-- Body 
  @component('mail::table')
  | Product       | Price         | Quntity  |
    @foreach($order->details as $details )
  |-----------------------------------------------------------------------------|
  | {{$details->product->title}} | {{$details->price}} | {{$details->quantity}} |
    @endforeach
  @endcomponent
--}}
  
@component('mail::button', ['url' => $order->invoice_url , 'color' => 'green' ])
  Download invoice
@endcomponent

  check url if button isn't work : {{$order->invoice_url}}

  All amount : {{$order->amount}} POUND <br>
  
Thanks,<br>
WOW SOLUTION
@endcomponent
