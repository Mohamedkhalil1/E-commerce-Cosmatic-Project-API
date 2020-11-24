<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

use App\Exports\DayOrder;
use App\Exports\EventOrders;
use App\Exports\orderMultiSheet;
use App\Exports\OrdersExport;
use App\Exports\OrdersViewExport;
use App\Exports\products;
use App\Exports\UsersExport;
use App\Exports\UsersFamilyOrders;
use App\Order;
use App\Product;
use Maatwebsite\Excel\Facades\Excel;

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();


Route::get('/download/orders/{date}',function($date){
    return Excel::download(new DayOrder($date),'orders.xlsx');
});


Route::get('/download',function(){
    return Excel::download(new OrdersViewExport,'orders.xlsx');
});

Route::get('/download/new/orders',function(){
    return Excel::download(new orderMultiSheet(),'families.xlsx');
});

Route::get('/download/family/orders',function(){
    return Excel::download(new UsersFamilyOrders,'orders.xlsx');
});


Route::get('/download/event/orders',function(){
    return Excel::download(new EventOrders ,'all_orders.xlsx');
});


Route::get('/download/products',function(){
    return Excel::download(new products ,'current_products.xlsx');
});



Route::get('/download/invoice/{order_num}',function($order_num){
    $order = Order::where('invoice_num',$order_num)->first();
    $products = $order->products;
    $details = $order->details;
    $user= $order->user;
    $pdf = PDF::loadView('pdf.invoice',['order' => $order , 'products' => $products , 'details' => $details , 'user' => $user]);
    return $pdf->download('invoice.pdf');
});


Route::get('/home', 'HomeController@index')->name('home');

Route::get('/create_tables','createTablesController@create_table');