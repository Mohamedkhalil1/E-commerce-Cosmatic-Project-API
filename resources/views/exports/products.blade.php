<table>
    <thead>
    <tr>
        <th><b>barcode</b></th>
        <th><b>title</b></th>
        <th><b>quantity</b></th>
        <th><b>Price</b></th>
        <th><b>Price Discount</b></th>
   
    </tr>
    </thead>
    <tbody>
    @foreach($products as $index => $product)
        <tr>
            <td>{{ (string)$product['barcode'] }}</td>
            <td>{{$product['title']}}</td>
            <td>{{$product['quantity']}}</td>
            <td>{{$product['price']}}</td>
            <td>{{$product['price_discount']}}</td>
        </tr>
    @endforeach
    </tbody>
</table>