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

//Route::middleware('auth:api')->get('/user', function (Request $request) {
//    return $request->user();
//});



    Route::post('/Signup', array('as' => 'Signup', 'uses' => 'ApiController@Signup'));
    Route::post('/Signin', array('as' => 'Signin', 'uses' => 'ApiController@Signin'));
    Route::post('/FindPassword', array('as' => 'FindPassword', 'uses' => 'ApiController@FindPassword'));
    Route::post('/GetUserInfo', array('as' => 'GetUserInfo', 'uses' => 'ApiController@GetUserInfo'));
    Route::post('/GetOrderList', array('as' => 'GetOrderList', 'uses' => 'ApiController@GetOrderList'));
    Route::post('/GetOrderHistory', array('as' => 'GetOrderHistory', 'uses' => 'ApiController@GetOrderHistory'));
    Route::post('/GetOrderOne', array('as' => 'GetOrderOne', 'uses' => 'ApiController@GetOrderOne'));
    Route::post('/GetOrderDetail', array('as' => 'GetOrderDetail', 'uses' => 'ApiController@GetOrderDetail'));
    Route::post('/GetSetting', array('as' => 'GetSetting', 'uses' => 'ApiController@GetSetting'));
    Route::post('/SetAPNCode', array('as' => 'SetAPNCode', 'uses' => 'ApiController@SetAPNCode'));
    Route::post('/SetGCMCode', array('as' => 'SetGCMCode', 'uses' => 'ApiController@SetGCMCode'));
    Route::post('/SetPaymentAccount', array('as' => 'setPaymentAccount', 'uses' => 'ApiController@setPaymentAccount'));
    Route::post('/GetBTClientToken', array('as' => 'GetBTClientToken', 'uses' => 'ApiController@GetBTClientToken'));
    Route::post('/CreateOrder', array('as' => 'CreateOrder', 'uses' => 'ApiController@CreateOrder'));
    Route::post('/ConfirmOrder', array('as' => 'ConfirmOrder', 'uses' => 'ApiController@ConfirmOrder'));
    Route::post('/ModifyPassword', array('as' => 'ModifyPassword', 'uses' => 'ApiController@ModifyPassword'));
    Route::post('/UpdateInfo', array('as' => 'UpdateInfo', 'uses' => 'ApiController@UpdateInfo'));
    Route::post('/ReceivePromoMail', array('as' => 'ReceivePromoMail', 'uses' => 'ApiController@ReceivePromoMail'));

    Route::get('/Signup', array('as' => 'Signup', 'uses' => 'ApiController@Signup'));
    Route::get('/Signin', array('as' => 'Signin', 'uses' => 'ApiController@Signin'));
    Route::get('/FindPassword', array('as' => 'FindPassword', 'uses' => 'ApiController@FindPassword'));
    Route::get('/GetUserInfo', array('as' => 'GetUserInfo', 'uses' => 'ApiController@GetUserInfo'));
    Route::get('/GetOrderList', array('as' => 'GetOrderList', 'uses' => 'ApiController@GetOrderList'));
    Route::get('/GetOrderHistory', array('as' => 'GetOrderHistory', 'uses' => 'ApiController@GetOrderHistory'));
    Route::get('/GetOrderOne', array('as' => 'GetOrderOne', 'uses' => 'ApiController@GetOrderOne'));
    Route::get('/GetOrderDetail', array('as' => 'GetOrderDetail', 'uses' => 'ApiController@GetOrderDetail'));
    Route::get('/GetSetting', array('as' => 'GetSetting', 'uses' => 'ApiController@GetSetting'));
    Route::get('/SetAPNCode', array('as' => 'SetAPNCode', 'uses' => 'ApiController@SetAPNCode'));
    Route::get('/SetGCMCode', array('as' => 'SetGCMCode', 'uses' => 'ApiController@SetGCMCode'));
    Route::get('/SetPaymentAccount', array('as' => 'SetPaymentAccount', 'uses' => 'ApiController@SetPaymentAccount'));
    Route::get('/GetBTClientToken', array('as' => 'GetBTClientToken', 'uses' => 'ApiController@GetBTClientToken'));
    Route::get('/CreateOrder', array('as' => 'CreateOrder', 'uses' => 'ApiController@CreateOrder'));
    // Route::get('/ConfirmOrder', array('as' => 'ConfirmOrder', 'uses' => 'ApiController@ConfirmOrder'));
    Route::get('/ModifyPassword', array('as' => 'ModifyPassword', 'uses' => 'ApiController@ModifyPassword'));

    Route::get('/testAPI', array('as' => 'testAPI', 'uses' => 'ApiController@testAPI'));
    Route::post('/checkPromo', array('as' => 'checkPromo', 'uses' => 'ApiController@checkPromo'));
    

    Route::get('/downloadOrder', array('as' => 'GetDownloadOrder', 'uses' => 'ApiController@GetDownloadOrder'));
    Route::get('/downloadedOrder/{id}', array('as' => 'SetDownloadedOrder', 'uses' => 'ApiController@SetDownloadedOrder'));

    Route::get('/test', array('as' => 'test', 'uses' => 'ApiController@sendPushTest'));

//Route::group(array('prefix' => 'api'), function() {
//    Route::post('/Signup', array('as' => 'Signup', 'uses' => 'ApiController@Signup'));
//    Route::post('/Signin', array('as' => 'Signin', 'uses' => 'ApiController@Signin'));
//    Route::post('/FindPassword', array('as' => 'FindPassword', 'uses' => 'ApiController@FindPassword'));
//    Route::post('/GetUserInfo', array('as' => 'GetUserInfo', 'uses' => 'ApiController@GetUserInfo'));
//    Route::post('/GetOrderList', array('as' => 'GetOrderList', 'uses' => 'ApiController@GetOrderList'));
//    Route::post('/GetOrderDetail', array('as' => 'GetOrderDetail', 'uses' => 'ApiController@GetOrderDetail'));
//    Route::post('/GetSetting', array('as' => 'GetSetting', 'uses' => 'ApiController@GetSetting'));
//    Route::post('/SetAPNCode', array('as' => 'SetAPNCode', 'uses' => 'ApiController@SetAPNCode'));
//    Route::post('/SetGCMCode', array('as' => 'SetGCMCode', 'uses' => 'ApiController@SetGCMCode'));
//    Route::post('/SetPaymentAccount', array('as' => 'setPaymentAccount', 'uses' => 'ApiController@setPaymentAccount'));
//    Route::post('/GetBTClientToken', array('as' => 'GetBTClientToken', 'uses' => 'ApiController@GetBTClientToken'));
//    Route::post('/CreateOrder', array('as' => 'CreateOrder', 'uses' => 'ApiController@CreateOrder'));
//    Route::post('/FileUpload', array('as' => 'FileUpload', 'uses' => 'ApiController@FileUpload'));
//    Route::post('/ConfirmOrder', array('as' => 'ConfirmOrder', 'uses' => 'ApiController@ConfirmOrder'));
//    Route::post('/CreateOrderAndroid', array('as' => 'CreateOrderAndroid', 'uses' => 'ApiController@CreateOrderAndroid'));
//    Route::post('/FileUploadAndroid', array('as' => 'FileUploadAndroid', 'uses' => 'ApiController@FileUploadAndroid'));
//    Route::post('/ConfirmOrderAndroid', array('as' => 'ConfirmOrderAndroid', 'uses' => 'ApiController@ConfirmOrderAndroid'));
//    Route::post('/ModifyPassword', array('as' => 'ModifyPassword', 'uses' => 'ApiController@ModifyPassword'));
//
//    Route::get('/Signup', array('as' => 'Signup', 'uses' => 'ApiController@Signup'));
//    Route::get('/Signin', array('as' => 'Signin', 'uses' => 'ApiController@Signin'));
//    Route::get('/FindPassword', array('as' => 'FindPassword', 'uses' => 'ApiController@FindPassword'));
//    Route::get('/GetUserInfo', array('as' => 'GetUserInfo', 'uses' => 'ApiController@GetUserInfo'));
//    Route::get('/GetOrderList', array('as' => 'GetOrderList', 'uses' => 'ApiController@GetOrderList'));
//    Route::get('/GetOrderDetail', array('as' => 'GetOrderDetail', 'uses' => 'ApiController@GetOrderDetail'));
//    Route::get('/GetSetting', array('as' => 'GetSetting', 'uses' => 'ApiController@GetSetting'));
//    Route::get('/SetAPNCode', array('as' => 'SetAPNCode', 'uses' => 'ApiController@SetAPNCode'));
//    Route::get('/SetGCMCode', array('as' => 'SetGCMCode', 'uses' => 'ApiController@SetGCMCode'));
//    Route::get('/SetPaymentAccount', array('as' => 'SetPaymentAccount', 'uses' => 'ApiController@SetPaymentAccount'));
//    Route::get('/GetBTClientToken', array('as' => 'GetBTClientToken', 'uses' => 'ApiController@GetBTClientToken'));
//    Route::get('/CreateOrder', array('as' => 'CreateOrder', 'uses' => 'ApiController@CreateOrder'));
//    Route::get('/FileUpload', array('as' => 'FileUpload', 'uses' => 'ApiController@FileUpload'));
//    Route::get('/ConfirmOrder', array('as' => 'ConfirmOrder', 'uses' => 'ApiController@ConfirmOrder'));
//    Route::get('/CreateOrderAndroid', array('as' => 'CreateOrderAndroid', 'uses' => 'ApiController@CreateOrderAndroid'));
//    Route::get('/FileUploadAndroid', array('as' => 'FileUploadAndroid', 'uses' => 'ApiController@FileUploadAndroid'));
//    Route::get('/ConfirmOrderAndroid', array('as' => 'ConfirmOrderAndroid', 'uses' => 'ApiController@ConfirmOrderAndroid'));
//    Route::get('/ModifyPassword', array('as' => 'ModifyPassword', 'uses' => 'ApiController@ModifyPassword'));
//
//    Route::get('/testAPI', array('as' => 'testAPI', 'uses' => 'ApiController@testAPI'));
//    Route::post('/checkPromo', array('as' => 'checkPromo', 'uses' => 'ApiController@checkPromo'));
//});