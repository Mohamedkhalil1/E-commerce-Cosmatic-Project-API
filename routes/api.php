<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group([
    'prefix' => 'auth'
], function ($router) {

    Route::post('login', 'Auth\AuthController@login');
    Route::post('logout', 'Auth\AuthController@logout');
    Route::post('refresh', 'Auth\AuthController@refresh');
    Route::post('register','Auth\AuthController@registeration');
    Route::post('register-social-media','Auth\AuthController@socialRegisteration');
    Route::post('login-social-media','Auth\AuthController@socialLogin');
});


Route::post('user/me', 'Auth\AuthController@me');

Route::resource('products', 'Product\ProductController',['except'=>['create','edit']]);
Route::post('products/excel', 'Product\ProductController@store_group');
Route::post('products/update/excel', 'Product\ProductController@update_group');
Route::post('products/update/pricing', 'Product\ProductController@update_pricing');
Route::post('products/view/{product_id}', 'Product\ProductController@view');
Route::get('products/variances/{product_id}', 'Product\ProductController@getVariances');



Route::resource('categories', 'Category\CategoryController',['except'=>['create','edit']]);
Route::resource('category.product', 'Category\CategoryProductController',['only' => ['update','destroy']]);


Route::resource('divisions', 'Devision\DevisionController',['except'=>['create','edit']]);
Route::resource('division.product', 'Devision\DevisionProductController',['only' => ['update','destroy']]);


Route::resource('brands', 'Brand\BrandController',['except'=>['create','edit']]);
Route::resource('brand.product', 'Brand\BrandProductController',['only' => ['update','destroy']]);
Route::resource('brand.category', 'Brand\BrandCategoryController',['only' => ['update','destroy']]);


Route::resource('my_orders', 'Order\MyOrderController',['except'=>['create','edit']]);
Route::resource('orders','Order\OrderController',['only' => ['index','show']]);


/* user */

Route::resource('user/cart', 'User\UserCardController',['except' => ['create','edit','update']]);
Route::post('user/cart/clear', 'User\UserCardController@clear_cart');
Route::resource('user/favourite', 'User\UserFavoruiteProduct',['except' => ['create','edit','update']]);
Route::post('user/favourite/clear', 'User\UserFavoruiteProduct@clear_favourite');

Route::post('user/profile/change-password','User\UserProfileController@change_password');
Route::post('user/profile/update','User\UserProfileController@update_data');

Route::resource('user/family', 'User\FamilyUserController',['only' => ['index','destroy']]);
Route::resource('user/notification', 'User\UserNotificationController',['only' => ['store']]);

/* contact us */
Route::resource('user/contact-us', 'User\UserContactUsController',['only' => ['store','index','show']]);


Route::post('phone/user/send_email' , 'User\UserForgetPasswordController@send_email');
Route::post('phone/user/match_code' , 'User\UserForgetPasswordController@match_code');
Route::post('phone/user/new_password' , 'User\UserForgetPasswordController@new_password');
Route::post('phone/user/resend_email' , 'User\UserForgetPasswordController@resend_email');




/* routes ads */
Route::resource('ads', 'Ad\AdController',['except' => ['create','edit']]);
Route::resource('web/ads', 'Ad\WebAdController',['only' => ['index','show']]);

/* Product type */
Route::resource('home/product/type', 'Product\ProductTypeController',['only' => ['index']]);



/* Event */
Route::resource('web/event', 'Event\Web\EventController',['except',['edit','create','index','update']]);
Route::post('web/events/{event_id}/activate','Event\Web\EventController@activate');
Route::post('web/events/{event_id}/image', 'Event\Web\EventController@set_image');

Route::resource('phone/event', 'Event\Phone\EventController',['only',['index']]);


Route::get('accept/callback/{token}', 'Order\MyOrderController@acceptCallback');

Route::get('r2s/callback', 'Order\MyOrderController@r2sCallback');

Route::post('my_orders/code', 'Order\MyOrderController@applyCode');


Route::get('companies', 'Product\ProductController@get_companies');

