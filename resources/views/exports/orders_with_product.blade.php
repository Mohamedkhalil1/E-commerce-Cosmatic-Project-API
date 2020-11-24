<table>
    <thead>
    <tr>
        <th>#</th>
        <th>Invoice NO.</th>
        <th><b>Name</b></th>
        <th><b>Email</b></th>
        <th><b>Phone</b></th>
        <th><b>Amount</b></th>
        <th><b>Product Title</b></th>
        <th><b>Price</b></th>
        <th><b>Quantity</b></th>
        <th><b>Barcode</b></th>
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
       @foreach($order->products as $index2 => $product)
            @if( $details->where('order_id',$order->id)->where('product_id',$product->id)->first()->quantity !== 0)
                <tr>
                    <td>{{ $index++ }}</td>
                    <td>{{ $order->invoice_num }}</td>
                
                    <td>{{ $order->user ? $order->user->name  : ''}}</td>
                    <td>{{ $order->user ? $order->user->email  : ''}}</td>
                    <td>{{ $order->user ? $order->user->phone  : ''}}</td>
                    <td>{{number_format((float)$order->amount, 2, '.', '')}} </td>

                    <td>{{ $product->title }}</td>
                    <td>{{ $product->family_price }}</td>
                    <td>{{ $details->where('order_id',$order->id)->where('product_id',$product->id)->first()->quantity }}</td>
                    <td>{{ $product->barcode}}</td>

                    <td>{{ $order->city }}</td>
                    <td>{{ $order->region }}</td>
                    <td>{{ $order->address }}</td>
                    <td>{{ $order->system_reference }}</td>

                    <td>{{ $order->created_at->format('d-m-Y h:m') }}</td>
                    <td>{{$order->invoice_url}}</td>
                    <td>{{$amount}}</td>
                </tr>
            @endif
        @endforeach
    @endforeach
    </tbody>
</table>