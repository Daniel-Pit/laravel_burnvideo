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

/*
Route::get('/', function () {
    return view('welcome');
});
*/
//  Admin Pages

Route::get('admin/login', array('as' => 'admin.login.get', 'uses' => 'AdminController@getLogin'));
Route::post('admin/login', array('as' => 'admin.login.post', 'uses' => 'AdminController@postLogin'));
Route::get('admin/logout', array('as' => 'admin.logout.get', 'uses' => 'AdminController@getLogout'));

Route::group(array('prefix' => 'admin', 'middleware' => 'auth.admin'), function() {
    Route::get('/', array('as' => 'admin.index.get', 'uses' => 'AdminController@getIndex'));
    Route::get('index', array('as' => 'admin.index.get', 'uses' => 'AdminController@getIndex'));
    Route::get('userList', array('as' => 'admin.userList', 'uses' => 'AdminController@userList'));
    Route::get('promocode', array('as' => 'admin.promoCodeList', 'uses' => 'AdminController@promoCodeList'));
    Route::get('mediaList', array('as' => 'admin.mediaList', 'uses' => 'AdminController@mediaList'));
    Route::get('notifyList', array('as' => 'admin.notifyList', 'uses' => 'AdminController@notifyList'));
    Route::get('notifyListById', array('as' => 'admin.notifyList', 'uses' => 'AdminController@notifyListById'));
    Route::get('messageList', array('as' => 'admin.messageList', 'uses' => 'AdminController@messageList'));
    Route::get('messageListById', array('as' => 'admin.messageListById', 'uses' => 'AdminController@messageListById'));
    Route::get('messageListByIds', array('as' => 'admin.messageListByIds', 'uses' => 'AdminController@messageListByIds'));
    Route::get('transactionList', array('as' => 'admin.transactionList', 'uses' => 'AdminController@transactionList'));
    Route::get('orderList', array('as' => 'admin.orderList', 'uses' => 'AdminController@orderList'));
    Route::get('setting', array('as' => 'admin.setting', 'uses' => 'AdminController@setting'));
    Route::get('calendar', array('as' => 'admin.calendar', 'uses' => 'AdminController@calendar'));
    Route::get('s3manage', array('as' => 'admin.s3Manage', 'uses' => 'AdminController@s3Manage'));  
    Route::get('blog', array('as' => 'admin.blog', 'uses' => 'AdminController@blog'));  
    Route::get('blog/create', array('as' => 'admin.createPost', 'uses' => 'AdminController@createPost'));  
    Route::get('blog/editPost/{id}', array('as' => 'admin.editPost', 'uses' => 'AdminController@editPost'));
    Route::get('blog/editPost/{id}', array('as' => 'admin.editPost', 'uses' => 'AdminController@editPost'));
    Route::get('blog/{id}/image', array('as' => 'admin.addImage', 'uses' => 'AdminController@addImage'));  
        
    Route::post('blog/create_category', array('as' => 'admin.createCategory', 'uses' => 'AdminController@ajax_category_create'));  
    Route::post('blog/save_post', array('as' => 'admin.savePost', 'uses' => 'AdminController@ajax_post_save'));  
    Route::post('blog/publish_post', array('as' => 'admin.publishPost', 'uses' => 'AdminController@ajax_post_publish'));  
    Route::post('blog/delete_post', array('as' => 'admin.deletePost', 'uses' => 'AdminController@ajax_post_delete'));  
    Route::post('blog/load_post', array('as' => 'admin.ajax_post_load', 'uses' => 'AdminController@ajax_post_load'));
    Route::post('blog/{id}/image', array('as' => 'admin.formAddImage', 'uses' => 'AdminController@formAddImage'));  

    
    Route::post('api_addUser', array('as' => 'admin.api_addUser', 'uses' => 'AdminController@api_addUser'));
    Route::post('api_addPromoCode', array('as' => 'admin.api_addPromoCode', 'uses' => 'AdminController@api_addPromoCode'));
    Route::post('api_editUser', array('as' => 'admin.api_editUser', 'uses' => 'AdminController@api_editUser'));
    Route::post('api_editPromoCode', array('as' => 'admin.api_editPromoCode', 'uses' => 'AdminController@api_editPromoCode'));
    Route::post('api_getUser', array('as' => 'admin.api_getUser', 'uses' => 'AdminController@api_getUser'));
    Route::post('api_getPromoCode', array('as' => 'admin.api_getPromoCode', 'uses' => 'AdminController@api_getPromoCode'));
    Route::post('api_deleteUser', array('as' => 'admin.api_deleteUser', 'uses' => 'AdminController@api_deleteUser'));
    Route::post('api_getMessage', array('as' => 'admin.api_getMessage', 'uses' => 'AdminController@api_getMessage'));
    Route::post('api_deleteMessage', array('as' => 'admin.api_deleteMessage', 'uses' => 'AdminController@api_deleteMessage'));
    Route::post('api_getNotify', array('as' => 'admin.api_getNotify', 'uses' => 'AdminController@api_getNotify'));
    Route::post('api_deleteNotify', array('as' => 'admin.api_deleteNotify', 'uses' => 'AdminController@api_deleteNotify'));
    Route::post('api_updateSet', array('as' => 'admin.api_updateSet', 'uses' => 'AdminController@api_updateSet'));

    Route::post('api_deleteOrder', array('as' => 'admin.api_deleteOrder', 'uses' => 'AdminController@api_deleteOrder'));
    Route::post('api_sendOrder', array('as' => 'admin.api_sendOrder', 'uses' => 'AdminController@api_sendOrder'));
    Route::post('api_convertOrder', array('as' => 'admin.api_convertOrder', 'uses' => 'AdminController@api_convertOrder'));
    Route::post('api_cancelOrder', array('as' => 'admin.api_cancelOrder', 'uses' => 'AdminController@api_cancelOrder'));
    Route::post('api_getShipArray', array('as' => 'admin.api_getShipArray', 'uses' => 'AdminController@api_getShipArray'));
    Route::post('api_searchUsers', array('as' => 'admin.api_searchUsers', 'uses' => 'AdminController@api_searchUsers'));
    Route::post('api_sendMail', array('as' => 'admin.api_sendMail', 'uses' => 'AdminController@api_sendMail'));
    Route::post('api_sendMailById', array('as' => 'admin.api_sendMailById', 'uses' => 'AdminController@api_sendMailById'));
    Route::post('api_sendMailByIds', array('as' => 'admin.api_sendMailByIds', 'uses' => 'AdminController@api_sendMailByIds'));
    Route::post('api_sendNotify', array('as' => 'admin.api_sendNotify', 'uses' => 'AdminController@api_sendNotify'));
    Route::post('api_sendNotifyById', array('as' => 'admin.api_sendNotifyById', 'uses' => 'AdminController@api_sendNotifyById'));

    Route::post('api_stateList', array('as' => 'admin.api_stateList', 'uses' => 'AdminController@api_stateList'));
    Route::post('api_cityList', array('as' => 'admin.api_cityList', 'uses' => 'AdminController@api_cityList'));  
	Route::post('api_uploadMailImage', array('as' => 'admin.api_mailImageUpload', 'uses' => 'AdminController@api_mailImageUpload'));
  
    Route::post('api_setOrderDvdTitle', array('as' => 'admin.api_setOrderDvdTitle', 'uses' => 'AdminController@api_setOrderDvdTitle'));
    Route::post('api_addCalendarEvent', array('as' => 'admin.api_addCalendarEvent', 'uses' => 'AdminController@api_addCalendarEvent'));
    Route::post('api_deleteCalendarEvent', array('as' => 'admin.api_deleteCalendarEvent', 'uses' => 'AdminController@api_deleteCalendarEvent'));
    Route::post('api_editAdmin', array('as' => 'admin.api_editAdmin', 'uses' => 'AdminController@api_editAdmin'));
    Route::post('api_deletes3', array('as' => 'admin.api_deleteS3', 'uses' => 'AdminController@api_deleteS3'));
});

//  User Pages

Route::group(array(), function() {
    Route::get('/', array('as' => 'index.get', 'uses' => 'FrontController@getIndex'));
    Route::get('how', array('as' => 'how.get', 'uses' => 'FrontController@getHow'));
    Route::get('about', array('as' => 'about.get', 'uses' => 'FrontController@getAbout'));
    Route::get('faq', array('as' => 'faq.get', 'uses' => 'FrontController@getFaq'));
    Route::get('blog', array('as' => 'blog.get', 'uses' => 'FrontController@getBlog'));
    Route::get('blog/{slug}', array('as' => 'blog.show', 'uses' => 'FrontController@showBlog'));
    Route::get('policies', array('as' => 'policies.get', 'uses' => 'FrontController@getPolicies'));
    Route::get('terms', array('as' => 'terms.get', 'uses' => 'FrontController@getTerms'));
    Route::get('contact', array('as' => 'contact.get', 'uses' => 'FrontController@getContact'));
    Route::post('contact', array('as' => 'contact.post', 'uses' => 'FrontController@postContact'));

    Route::get('signup', array('as' => 'signup.get', 'uses' => 'FrontController@getSignup'));
    Route::post('signup', array('as' => 'signup.post', 'uses' => 'FrontController@postSignup'));
    Route::get('activate', array('as' => 'account.register', 'uses' => 'FrontController@getRegisterUser'));
    Route::get('login', array('as' => 'login.get', 'uses' => 'FrontController@getLogin'));
    Route::post('login', array('as' => 'login.post', 'uses' => 'FrontController@postLogin'));
    Route::get('logout', array('as' => 'logout.get', 'uses' => 'FrontController@getLogout'));
    Route::get('forgot', array('as' => 'forgot.get', 'uses' => 'FrontController@getForgot'));
    Route::post('forgot', array('as' => 'forgot.post', 'uses' => 'FrontController@postForgot'));
});

Route::group(array('middleware' => 'auth.front'), function() {
    Route::get('order', array('as' => 'order.get', 'uses' => 'FrontController@getOrder'));
    Route::get('order-history', array('as' => 'order-history.get', 'uses' => 'FrontController@getOrderHistory'));
    Route::post('ajax-pay', array('as' => 'ajax-pay.post', 'uses' => 'FrontController@ajaxPay'));
    Route::post('file-upload', array('as' => 'file-upload.post', 'uses' => 'FrontController@postFileUpload'));
    Route::post('file-delete', array('as' => 'file-delete.post', 'uses' => 'FrontController@postFileDelete'));
    Route::get('register-card', array('as' => 'register-card.get', 'uses' => 'FrontController@getRegisterCard'));
    Route::post('register-card', array('as' => 'register-card.post', 'uses' => 'FrontController@postRegisterCard'));
    Route::post('ajax-register-card', array('as' => 'ajax-register-card.post', 'uses' => 'FrontController@ajaxRegisterCard'));
    Route::get('settings', array('as' => 'settings.get', 'uses' => 'FrontController@getSettings'));
    Route::post('settings', array('as' => 'settings.post', 'uses' => 'FrontController@postSettings'));
    Route::get('media-upload','FrontController@postUploadFileToS3');
    Route::post('media-upload','FrontController@postUploadFileToS3');
});


Route::post('check-promo', array('as' => 'check-promo.post', 'uses' => 'FrontController@checkPromo'));

Route::get('/clearCache', function(){
    Artisan::call('cache:clear');
    dd('Success');
});
Route::get('/clearView', function(){
    Artisan::call('view:clear');
    dd('Success');
});
Route::get('/configCache', function(){
    Artisan::call('config:cache');
    dd('Success');
});

//api part
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