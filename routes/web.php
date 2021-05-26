<?php

use Illuminate\Support\Facades\Route;

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

//get files for displaying.
Route::get('files/{filename}', function ($filename){

    $filename = urldecode($filename);
    
    $filename = my_laravelDecode($filename);
    $path = storage_path('app/' . $filename);

    if (!File::exists($path)) {
        abort(404);
    }

    $file = File::get($path);
    $type = File::mimeType($path);

    $response = Response::make($file, 200);
    $response->header("Content-Type", $type);
//    $response->header('Content-Disposition', 'attachment');
    return $response;
});
Route::get('avatar/{filename}', function ($filename){

    $filename = urldecode($filename);
    
    $filename = my_laravelDecode($filename);
    $path = storage_path('app/avatars/' . $filename);

    if (!File::exists($path)) {
        abort(404);
    }

    $file = File::get($path);
    $type = File::mimeType($path);

    $response = Response::make($file, 200);
    $response->header("Content-Type", $type);
//    $response->header('Content-Disposition', 'attachment');
    return $response;
});


function my_laravelDecode($param)
{
    $param = str_replace('>', '/', $param);
    return $param;
}

Route::group([
    'middleware' => 'web',
], function ($router) {
    Route::get('/', function () {
        return view('admin');
    });
    Route::get('/admin', function () {
        return view('admin');
    });
    Route::get('/manage-logo', function () {
        return view('manage-logo');
    });
    Route::get('/manage-users', function () {
        return view('manage-users');
    });
    Route::get('/manage-photos', function () {
        return view('manage-photos');
    });
    Route::get('/manage-pages', function () {
        return view('manage-pages');
    });
    Route::get('/view-user-files', function () {
        return view('view-user-files');
    });
    Route::get('/page', function () {
        return view('page');
    });
    Route::get('/add-page', function () {
        return view('add-page');
    });
    Route::get('/edit-page', function () {
        return view('edit-page');
    });
    Route::get('/edit-user', function () {
        return view('edit-user');
    });
    
    Route::post('/manage-logo', function () {
        return view('manage-logo');
    });
    Route::post('/ajax/delete_user', function () {
        return view('ajax.delete_user');
    });
    Route::post('/edit-user', function () {
        return view('edit-user');
    });
    Route::post('/ajax/delete_page', function () {
        return view('ajax.delete_page');
    });
    Route::post('/add-page', function () {
        return view('add-page');
    });
    Route::post('/edit-page', function () {
        return view('edit-page');
    });
    Route::post('/ajax/delete_photo', function () {
        return view('ajax.delete_photo');
    });
    Route::post('/ajax/load_more_admin_photos', function () {
        return view('ajax.load_more_admin_photos');
    });
});
