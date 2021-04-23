<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Response;

class FileUploadController extends Controller {

    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct() {
        $this->middleware('fileupload:api', ['except' => ['upload']]);
    }

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function upload(Request $request){
        //$file = $request->file('file');
        //$filePath = $file->storeAs('uploads', $file->getClientOriginalName(), 'public');
        // if($file){
        //     $fileName = time().'_'.$file->getClientOriginalName();
        //     $filePath = $file->storeAs('uploads', $fileName, 'public');
        //     echo $filePath;
        //     return back()
        //     ->with('success','File has been uploaded.')
        //     ->with('file', $fileName);
        // }
        $request->file('file')->store('images');
        $response = Response::make('pass some success message to flow.js', 200);
        return $response;

        // $uuid = (string) Str::uuid();
        // $request = new \Flow\Request();
        // $destination = storage_path().'/files/flow/uploads/'.$uuid;
        // $config = new \Flow\Config(array(
        //     'tempDir' => storage_path().'/files/flow/chunks'
        // ));
        // $file = new \Flow\File($config, $request);
        // $response = Response::make('', 200);

        // if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        //     if (!$file->checkChunk()) {
        //         return Response::make('', 404);
        //     }
        // } else {
        //     if ($file->validateChunk()) {
        //         $file->saveChunk();
        //     } else {
        //         // error, invalid chunk upload request, retry
        //         return Response::make('', 400);
        //     }
        // }
        // if ($file->validateFile() && $file->save($destination)) {
        //     $response = Response::make('pass some success message to flow.js', 200);
        // }
        // return $response;
    }

    
}
