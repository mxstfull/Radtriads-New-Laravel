<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Response;
use App\Models\AlbumModel;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class FileUploadController extends Controller {

    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct() {
        $this->middleware('fileupload:api', ['except' => ['upload', 'getSubFolders', 'createFolder']]);
    }

    

    /**
     * File Upload.
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
    
    /**
     * Folder Creation.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getSubFolders(Request $request){

        $user_id = $request->input('user_id');
        $currentFolder = $request->input('currentFolder');

        $result = [];
        $result_tmp = AlbumModel::select('id', 'title', 'is_protected', 'path')
            ->where('path', 'like', $currentFolder['path']."%")
            ->where('user_id', $user_id)
            ->orderby('updated_at', 'asc')
            ->get();
        
        foreach($result_tmp as $item) {
            $trimmed = str_replace($currentFolder['path'], '', $item['path']) ;
            if(!strcasecmp($trimmed, $item['title']."/"))
            {
                array_push($result, $item);
            }    
        }
        // print $result[0];
        return response()->json($result);
    }

    /**
     * Folder Creation.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function createFolder(Request $request){
        $user_id = $request->input('user_id');
        $currentFolder = $request->input('currentFolder');
        $newFolderTitle = $request->input('newFolderTitle');
        $newFolderPath = $currentFolder['path'].$newFolderTitle;
        $data = array('user_id' => $user_id, 'title' => $newFolderTitle, 'path' => $newFolderPath."/");
        
        if(File::isDirectory(storage_path('app/').$newFolderPath."/"))
            return "insert error";
        
        if(!AlbumModel::create($data))
            return "insert error";
        
        if(!Storage::disk('local')->makeDirectory($newFolderPath))
        {
            AlbumModel::select('id', 'title', 'is_protected', 'path')
                ->where('path', 'like', $currentFolder['path']."%")
                ->where('user_id', $user_id)
                ->orderby('id', 'desc')
                ->take(1)
                ->delete();
        }
        return response()->json(
            AlbumModel::select('id', 'title', 'is_protected', 'path')
                ->where('path', 'like', $currentFolder['path']."%")
                ->where('user_id', $user_id)
                ->orderby('id', 'desc')
                ->get()
                ->first()
        );
    }
    
}
