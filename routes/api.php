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



/* Products */
Route::resource('products', 'Product\ProductController',['except'=>['create','edit']]);
Route::post('products/view/{product_id}', 'Product\ProductController@view');
Route::get('products/variances/{product_id}', 'Product\ProductController@getVariances');


/* Categories */
Route::resource('categories', 'Category\CategoryController',['except'=>['create','edit']]);

/* Divisions */
Route::resource('divisions', 'Devision\DevisionController',['except'=>['create','edit']]);

/* Brands  */
Route::resource('brands', 'Brand\BrandController',['only'=>['show','index']]);


/* Orders */
Route::resource('my_orders', 'Order\MyOrderController',['only'=>['show','inex']]);
Route::post('my_orders/code', 'Order\MyOrderController@applyCode');


/* user */
Route::post('user/me', 'Auth\AuthController@me');

/* Cart */
Route::resource('user/cart', 'User\UserCardController',['except' => ['index','store','destroy']]);
Route::post('user/cart/clear', 'User\UserCardController@clear_cart');

/* Favoruites */
Route::resource('user/favourite', 'User\UserFavoruiteProduct',['except' => ['create','edit','update']]);
Route::post('user/favourite/clear', 'User\UserFavoruiteProduct@clear_favourite');

/* Profile  */
Route::post('user/profile/change-password','User\UserProfileController@change_password');
Route::post('user/profile/update','User\UserProfileController@update_data');


/* contact us */
Route::resource('user/contact-us', 'User\UserContactUsController',['only' => ['store','index','show']]);

/* forget password */
Route::post('phone/user/send_email' , 'User\UserForgetPasswordController@send_email');
Route::post('phone/user/match_code' , 'User\UserForgetPasswordController@match_code');
Route::post('phone/user/new_password' , 'User\UserForgetPasswordController@new_password');
Route::post('phone/user/resend_email' , 'User\UserForgetPasswordController@resend_email');


/* routes ads */
Route::resource('ads', 'Ad\AdController',['only' => ['show','index']]);

