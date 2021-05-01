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
    'prefix' => 'fileview'
], function ($router) {
    Route::post('getFileByCategory', 'FileViewController@getFileByCategory');
    Route::post('downloadFiles', 'FileViewController@downloadFiles');
});

