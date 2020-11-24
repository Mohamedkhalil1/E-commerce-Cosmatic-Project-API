<table>
    <thead>
    <tr>
        <th>#</th>
        <th>Invoice NO.</th>
        <th><b>Name</b></th>
        <th><b>Staff Email</b></th>
        <th><b>Email</b></th>
        <th><b>Phone</b></th>
        <th><b>Amount</b></th>
        <th>Shipping</th>
        <th><b>City</b></th>
        <th><b>Region</b></th>
        <th><b>Address</b></th>
        <th><b>System Reference</b></th>
        <th><b>Date</b></th>
        <th><b>Invoice Url</b></th>

        <th><b>TOTAL AMOUNT OF DAY </b></th>
    </tr>
    </thead>
    <tbody>
    @foreach($orders as $index => $order)
       
        <tr>
            <td>{{ $index++ }}</td>
            <td>{{ $order->invoice_num }}</td>
        
            <td>{{ $order->user ? $order->user->name  : ''}}</td>
            <td>{{ $order->user ? $order->user->parent_id === null ? '' : $order->user->staff->email : ''}}</td>
            <td>{{ $order->user ? $order->user->email  : ''}}</td>
            <td>{{ $order->user ? $order->user->phone  : ''}}</td>
            <td>{{number_format((float)$order->amount - $order->shipping_fees, 2, '.', '')}} </td>
            <td>{{number_format((float)$order->shipping_fees, 2, '.', '')}} </td>
            
            <td>{{ $order->city }}</td>
            <td>{{ $order->region }}</td>
            <td>{{ $order->address }}</td>
            <td>{{ $order->system_reference }}</td>

            <td>{{ $order->created_at->format('d-m-Y h:m') }}</td>
            <td>{{$order->invoice_url}}</td>
            <td>{{$amount}}</td>
        </tr>
         
    @endforeach
    </tbody>
</table>