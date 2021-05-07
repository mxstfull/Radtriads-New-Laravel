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

Route::get('/', function () {
    return view('welcome');
});
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
function my_laravelDecode($param)
{
    $param = str_replace('>', '/', $param);
    return $param;
}
