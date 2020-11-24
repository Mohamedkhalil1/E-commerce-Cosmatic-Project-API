<?php
   header('Content-Type: text/html; charset=utf-8'); 
?>
<head>
    <link href="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
    <script src="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
</head>
<!------ Include the above in your HEAD tag ---------->

<style>

  #invoice{
    padding: 30px;
}

.invoice {
    position: relative;
    background-color: #FFF;
    min-height: 680px;
    padding: 15px
}

.invoice header {
    padding: 10px 0;
    margin-bottom: 20px;
    border-bottom: 1px solid #3989c6
}

.invoice .company-details {
    text-align: right
}

.invoice .company-details .name {
    margin-top: 0;
    margin-bottom: 0
}

.invoice .contacts {
    margin-bottom: 20px
}

.invoice .invoice-to {
    text-align: left
}

.invoice .invoice-to .to {
    margin-top: 0;
    margin-bottom: 0
}

.invoice .invoice-details {
    text-align: right
}

.invoice .invoice-details .invoice-id {
    margin-top: 0;
    color: #3989c6
}

.invoice main {
    padding-bottom: 50px
}

.invoice main .thanks {
    margin-top: -100px;
    font-size: 2em;
    margin-bottom: 50px
}

.invoice main .notices {
    padding-left: 6px;
    border-left: 6px solid #3989c6
}

.invoice main .notices .notice {
    font-size: 1.2em
}

.invoice table {
    width: 100%;
    border-collapse: collapse;
    border-spacing: 0;
    margin-bottom: 20px
}

.invoice table td,.invoice table th {
    padding: 5px;
    background: #eee;
    border-bottom: 1px solid #fff;
    text-align: center;
}

.invoice table th {
    white-space: nowrap;
    font-weight: 400;
    font-size: 16px
}

.invoice table td h3 {
    margin: 0;
    font-weight: 400;
    color: #3989c6;
    font-size: 1em
}

.invoice table .qty,.invoice table .total,.invoice table .unit {
    text-align: right;
    font-size: 0.5em
}

.invoice table .no {
    color: #fff;
    font-size: 0.6em;
    background: #3989c6
}

.invoice table .unit {
    background: #ddd
}

.invoice table .total {
    background: #3989c6;
    color: #fff
}

.invoice table tbody tr:last-child td {
    border: none
}

.invoice table tfoot td {
    background: 0 0;
    border-bottom: none;
    white-space: nowrap;
    text-align: right;
    padding: 5px 20px;
    font-size: 0.5em;
    border-top: 1px solid #aaa
}

.invoice table tfoot tr:first-child td {
    border-top: none
}

.invoice table tfoot tr:last-child td {
    color: #3989c6;
    font-size: 1.4em;
    border-top: 1px solid #3989c6
}

.invoice table tfoot tr td:first-child {
    border: none
}

.invoice footer {
    width: 100%;
    text-align: center;
    color: #777;
    border-top: 1px solid #aaa;
    padding: 8px 0
}

@media print {
    .invoice {
        font-size: 11px!important;
        overflow: hidden!important
    }

    .invoice footer {
        position: absolute;
        bottom: 10px;
        page-break-after: always
    }

    .invoice>div:last-child {
        page-break-before: always
    }
}

.ar_address{
    font-family: 'Cairo', sans-serif;
}

  </style>

<div id="invoice">
    <div class="invoice overflow-auto">
        <div style="min-width: 600px">
            <header>
                <div class="row">
                    <div class="col company-details">
                        <h2 class="name">
                            <a target="_blank" href="#">
                                <img src="http://thefamilysale.net/loreal_backend/logo.jpg" />
                            </a>
                        </h2>
                        <div>+020102659879</div>
                        <div>family_sales@thefamilysale.net</div>
                    </div>
                </div>
            </header>
            <main>
                <div class="row contacts">
                    <div class="col invoice-to">
                        <div class="text-gray-light">INVOICE TO:</div>
                        <h2 class="to">{{$user->name}}</h2>

                        <div class="address">{{$order->city}}</div>
                        <div class="address">{{$order->region}}</div>

                        <div class="ar_address">{{$order->address}}</div>
                        
                        <div class="email">{{$user->email}}</div>
                        <div class="email">{{$user->phone}}</div>
                    </div>
                    <div class="col invoice-details">
                      <h1 class="invoice-id">INVOICE {{$order->invoice_num}}</h1>
                        <div class="date">Date of Invoice: {{$order->created_at->format('d-m-Y')}}</div>
                    </div>
                </div>
                <table border="0" cellspacing="0" cellpadding="0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th class="text-left">Barcode</th>
                            <th class="text-left">Product</th>
                            <th class="text-right">Price</th>
                            <th class="text-right">Quantity</th>
                            <th class="text-right">Total</th>
                            
                        </tr>
                    </thead>
                    <tbody>
                      @foreach ($order->products()->orderBy('quantity','desc')->get() as $index => $product)
                        @if($details->where('product_id',$product->id)->first()->quantity === 0)
                        @else
                            <tr>
                                <td>{{ $index+1 }}</td>
                                <td>{{$product->barcode}}</td>
                                <td>{{ $product->title }}</td>
                                <td>
                                    {{$details->where('product_id',$product->id)->first()->price_discond .' EGP'}}
                                </td>
                                <td>{{ $details->where('product_id',$product->id)->first()->quantity }}</td>
                                <td>
                                    {{$details->where('product_id',$product->id)->first()->price_discount * $details->where('product_id',$product->id)->first()->quantity}} EGP
                                </td>
                            </tr>
                          @endif
                        @endforeach
                    </tbody>
                </table>
                
                <div class="notices">
                    <div>SHIPPING FEES: {{(int)$order->shipping_fees === 0 ? 'FREE' : $order->shipping_fees.' EGP'}}</div>
                    <div>TOTAL AMOUNT: {{number_format((float)$order->amount, 2, '.', '')}} EGP</div>
                </div>
            </main>
            <footer>
                Invoice was created on a computer and is valid without the signature and seal.
            </footer>
        </div>
        <!--DO NOT DELETE THIS div. IT is responsible for showing footer always at the bottom-->
        <div></div>
    </div>
</div>