<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;

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
        $file = $request->file('attachment');
        $filePath = $file->storeAs('uploads', $file->getClientOriginalName(), 'public');
        // if($file){
        //     $fileName = time().'_'.$file->getClientOriginalName();
        //     $filePath = $file->storeAs('uploads', $fileName, 'public');
        //     echo $filePath;
        //     return back()
        //     ->with('success','File has been uploaded.')
        //     ->with('file', $fileName);
        // }
    }
}
