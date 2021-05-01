<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Response;
use App\Models\AlbumModel;
use App\Models\FileModel;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use App\User;

use Pion\Laravel\ChunkUpload\Exceptions\UploadMissingFileException;
use Pion\Laravel\ChunkUpload\Handler\AbstractHandler;
use Pion\Laravel\ChunkUpload\Handler\HandlerFactory;
use Pion\Laravel\ChunkUpload\Receiver\FileReceiver;
use Pion\Laravel\ChunkUpload\Exceptions\UploadFailedException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;


class FileUploadController extends Controller {

    public $initial_path;
    private $user_id;
    private $unique_id;
    private $is_picture = 1;
    private $ip_address;
    private $category;
    private $currentPathForUpload;
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct() {
        $this->middleware('fileupload:api', ['except' => ['upload', 'getSubFolders', 'createFolder', 'getFolderTree']]);
    }
    /**
     * File Upload.
     *
     * @return \Illuminate\Http\JsonResponse
     */

    public function upload(Request $request){

        // $img = $request->file('image');
        // $img = $file;
        // $image = '123.'.$img->getClientOriginalExtension();
        // $destination = 'uploads/';
        // $img->move($destination, $image);
        
        $this->user_id = $request->input('user_id');
        $this->unique_id = $request->input('unique_id');
        $this->currentPathForUpload = $request->input('currentPath');
        $this->ip_address = $request->ip();
        $this->category = 0;
        //create the file receiver
        $receiver = new FileReceiver("file", $request, HandlerFactory::classFromRequest($request));

        // check if the upload is success, throw exception or return response you need
        if ($receiver->isUploaded() === false) {
            throw new UploadMissingFileException();
        }

        // receive the file
        $save = $receiver->receive();

        // check if the upload has finished (in chunk mode it will send smaller files)
        if ($save->isFinished()) {
            // save the file and return any response you need, current example uses `move` function. If you are
            // not using move, you need to manually delete the file by unlink($save->getFile()->getPathname())
            return $this->saveFile($save->getFile());
        }

        // we are in chunk mode, lets send the current progress
        /** @var AbstractHandler $handler */
        $handler = $save->handler();

        return response()->json([
            "done" => $handler->getPercentageDone(),
        ]);
    }

    protected function saveFile(UploadedFile $file)
    {
        $fileName = $this->createFilename($file);
        $currentPath = $this->unique_id;
        if(!empty($this->currentPathForUpload))
            $currentPath = $currentPath.'/'.$this->currentPathForUpload;

        // Build the file path
        $filePath = "uploads/{$currentPath}/";
        $finalPath = storage_path("app/".$filePath);
    
        // move the file name
        $file->move($finalPath, $fileName);

        $file = Storage::get($filePath.$fileName);
    
        //Simba: insert Database.
        $short_id = gen_uid(8);
        $title = $fileName;
        $unique_id = $this->unique_id;
        $url = $filePath.$fileName;
        $folder_path = $filePath;
        $filename = $title;
        $ext = pathinfo(storage_path($filePath.$fileName), PATHINFO_EXTENSION);
        $diskspace = Storage::size($filePath.$fileName);
        $bandwidth = Storage::size($filePath.$fileName);
        $ip_address = $this->ip_address;
        $user_id = $this->user_id;
        $is_picture = $this->is_picture;
        $category = $this->category;
        $data = array (
            'short_id' => $short_id,
            'title' => $title,
            'unique_id' => $unique_id,
            'url' => $url,
            'folder_path' => $folder_path,
            'filename' => $filename,
            'ext' => $ext,
            'diskspace' => $diskspace,
            'bandwidth' => $bandwidth,
            'ip_address' => $ip_address,
            'user_id' => $user_id,
            'is_picture' => $is_picture,
            'category' => $category
        );
        FileModel::create($data);

        return response()->json([
            'path' => $filePath,
            'name' => $fileName
        ]);
    }    
    
    protected function createFilename(UploadedFile $file)
    {
        $filename = $file->getClientOriginalName();   
        return $filename;
    }

    /**
     * Folder Creation.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getSubFolders(Request $request){

        $user_id = $request->input('user_id');
        $currentFolder = $request->input('currentFolder');
        $result_tmp = scandir(storage_path('app/').$currentFolder['path']."/");
        $result_tmp = AlbumModel::select('id', 'title', 'is_protected', 'path')
                ->where('path', 'like', $currentFolder['path']."%")
                ->where('user_id', $user_id)
                ->orderby('id', 'asc')
                ->get();
        $result = [];
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

    // get Folder Tree.
    public function getFolderTree(Request $request) {
        $user_id = $request->input('user_id');
        $unique_id = $request->input('unique_id');
        $this->initial_path = storage_path('app/uploads/').$unique_id;
        if(!is_dir($this->initial_path))
            File::makeDirectory($this->initial_path);
        $result = array(4);
        $categoryArray = ['photos', 'music', 'video', 'code'];
        for ($x = 0; $x < 4; $x++) {
            $result[$x] = $this->getAllSubFolders($this->initial_path, 'Home', $categoryArray[$x]);
        }
        return json_encode($result);
    }
    private function getAllSubFolders($currentPath, $folder_title, $category) {
        if($currentPath === 'Home')
            $path = str_replace($this->initial_path, '', $currentPath);
        else $path = str_replace($this->initial_path.'/', '', $currentPath);
        
        $folderItem =  [
            'displayName' => $folder_title,
            'iconName' => 'person',
            'path' => $path,
            'category' => $category
        ];
        $folderItem['children'] = [];
        $files = scandir($currentPath);
        foreach ($files as $file) {
            if(!in_array($file, array(".","..")))
            {
                if(!is_dir($currentPath.'/'.$file)) continue;
                $folderItem['children'][] = $this->getAllSubFolders($currentPath.'/'.$file, $file, $category);
            }
        }
        return $folderItem;
    }
}
