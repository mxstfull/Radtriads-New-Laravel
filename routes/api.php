<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::group([
    'middleware' => 'api',
    'prefix' => 'auth'
], function ($router) {
    Route::post('login', 'AuthController@login');
    Route::post('register', 'AuthController@register');
    Route::post('logout', 'AuthController@logout');
    Route::post('refresh', 'AuthController@refresh');
    Route::get('user-profile', 'AuthController@userProfile');
    Route::post('verifyUser' , 'AuthController@verifyUser');
});

Route::group([
    'middleware' => 'api',
    'prefix' => 'fileupload'
], function ($router) {
    Route::post('upload', 'FileUploadController@upload');
    Route::post('getSubFolders', 'FileUploadController@getSubFolders');
    Route::post('createFolder', 'FileUploadController@createFolder');
    Route::post('getFolderTree', 'FileUploadController@getFolderTree');
});

Route::group([
    'middleware' => 'api',
    'prefix' => 'account'
], function ($router) {
    Route::post('GetUserData', 'AccountController@GetUserData');
    Route::post('MyInfo', 'AccountController@MyInfo');
    Route::post('Settings', 'AccountController@Settings');
    Route::post('Privacy', 'AccountController@Privacy');
    Route::post('delete' , 'AccountController@delete');
    Route::post('getDiskUsage' , 'AccountController@getDiskUsage');
});

Route::group([
    'middleware' => 'api',
    'prefix' => 'stripe'
], function ($router) {
    Route::post('request_url', 'PlanController@request_url');
    Route::post('payment_webhook', 'PlanController@webhook');
});

Route::group([
    'middleware' => 'api',
    'prefix' => 'fileview'
], function ($router) {
    Route::post('getFileByCategory', 'FileViewController@getFileByCategory');
    Route::post('downloadFiles', 'FileViewController@downloadFiles');
    Route::post('moveFiles', 'FileViewController@moveFiles');
    Route::post('editFilePrivacy', 'FileViewController@editFilePrivacy');
    Route::post('renameFile', 'FileViewController@renameFile');
    Route::post('deleteFile', 'FileViewController@deleteFile');
    Route::post('renameAlbum', 'FileViewController@renameAlbum');
    Route::post('recoverFiles', 'FileViewController@recoverFiles');
    Route::post('permanentlyDeleteFiles', 'FileViewController@permanentlyDeleteFiles');
    Route::post('getItemByUniqueId', 'FileViewController@getItemByUniqueId');
});

