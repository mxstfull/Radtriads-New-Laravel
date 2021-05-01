<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Response;
use App\Models\AlbumModel;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use App\Models\FileModel;
use ZipArchive;

class FileViewController extends Controller
{
    //
    public $filterArray = array (
        'photo' => array('jpg', 'jpeg', 'png', 'git', 'tif'),
        'music' => array('mp3', 'wav'),
        'video' => array('mp4', 'mov', 'swf', 'flv'),
        'code' => array('txt', 'rtf', 'html', 'html5', 'webm', 'php', 'css', 'xml', 'json', 'pdf', 'docx', 'xlsx', 'pptx', 'java')
    );
    public function getFileByCategory(Request $request)
    {
        $unique_id = $request->input('unique_id');
        $user_id = $request->input('user_id');
        $currentPath = $request->input('currentPath');
        if($currentPath == "home") $currentPath = "";
        if(!empty($currentPath)) $currentPath = $currentPath.'/';
        $category = $request->input('category');
        // $this->filterArray[$category]
        $realPath = storage_path('app/uploads/').$unique_id.$currentPath; 
        
        $folderPath = 'uploads/'.$unique_id.'/'.$currentPath; 
        // $folderPath = str_replace(' ', '%20', $folderPath);
        
        $result = FileModel::select('unique_id', 'title', 'url', 'filename', 'diskspace', 'category', 'is_protected', 'updated_at')
                ->where('folder_path', 'like', $folderPath)
                ->where('user_id', $user_id)
                ->where('is_deleted', 0)
                ->where('category', $category)
                ->orderby('updated_at', 'desc')
                ->get();
        return response()->json($result);
    }
    public function downloadFiles(Request $request) {
        
        
        $public_dir=public_path();
        // Zip File Name
        $zipFileName = 'AllDocuments.zip';
        // Create ZipArchive Obj
        $zip = new ZipArchive;
        if ($zip->open($public_dir . '/' . $zipFileName, ZipArchive::CREATE) === TRUE) {
            // Add File in ZipArchive
            foreach($files as $file) {
                $zip->addFile($file->path, $file->name);
            } 
            $zip->close();
        }
        // Set Header
        $headers = array(
            'Content-Type' => 'application/octet-stream',
        );
        $filetopath=$public_dir.'/'.$zipFileName;
        // Create Download Response
        if(file_exists($filetopath)){
            return response()->download($filetopath,$zipFileName,$headers);
        }
    }
}
