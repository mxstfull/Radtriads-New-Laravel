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
        
        $result = FileModel::select('unique_id', 'title', 'url', 'filename', 'diskspace', 'category', 'is_protected', 'created_at', 'updated_at')
                ->where('folder_path', 'like', $folderPath)
                ->where('user_id', $user_id)
                ->where('is_deleted', 0)
                ->where('category', $category)
                ->orderby('created_at', 'desc')
                ->get();
        return response()->json($result);
    }
    public function downloadFiles(Request $request) {
        
        $fileList = $request->input('fileList');
        $public_dir=public_path();
        // Zip File Name
        $zipFileName = date("Y_m_d_his").'.zip';
        // Create ZipArchive Obj
        $zip = new ZipArchive;
        if ($zip->open($public_dir . '/' . $zipFileName, ZipArchive::CREATE) === TRUE) {
            // Add File in ZipArchive
            foreach($fileList as $file) {
                $zip->addFile(storage_path('App/').$file["url"], $file["title"]);
            } 
            $zip->close();
        }
        // Set Header
        $headers = array(
            'Content-Type' => 'application/zip',
        );
        $filetopath=$public_dir.'/'.$zipFileName;
        // Create Download Response
        if(file_exists($filetopath)){
            // return response()->download($filetopath,$zipFileName,$headers);
            return Response::download($filetopath, $zipFileName, $headers)->deleteFileAfterSend(true);
        }
    }

    public function moveFiles(Request $request) {

        $user_unique_id = $request->input('unique_id');
        $user_id = $request->input('user_id');
        $destPath = $request->input('destPath');
        $action = $request->input('action');
        if($destPath == 'home') $destPath = "";
        $fileList = $request->input('fileList');
        foreach($fileList as $filePath) {
            if(storage_path('app/').$filePath['url'] == storage_path('app/uploads/').$user_unique_id.'/'.($destPath != "" ? $destPath.'/': '').$filePath['title']) continue;
            $file_title = $filePath['title'];
            while(File::exists(storage_path('app/uploads/').$user_unique_id.'/'.($destPath != "" ? $destPath.'/': '').$file_title))
            {
                $temp = explode('.', $file_title);
                $title = '';
                for($index = 0; $index < sizeof($temp); $index ++)
                {
                    if($index < sizeof($temp) - 1)
                        $title = $title.$temp[$index];
                    else $title = $title.'_copy.'.$temp[$index];
                }
                $file_title = $title;
            }
            if($action == "Move") {
                if(File::move(storage_path('app/').$filePath['url'], storage_path('app/uploads/').$user_unique_id.'/'.($destPath != "" ? $destPath.'/': '').$file_title)) {
                    $unique_id = $filePath['unique_id'];
                    FileModel::where('unique_id', $unique_id)
                        ->update([
                            'url' => 'uploads/'.$user_unique_id.'/'.($destPath != "" ? $destPath.'/': '').$file_title, 
                            'folder_path' =>'uploads/'.$user_unique_id.'/'.($destPath != "" ? $destPath.'/': ''),
                            'title' => $file_title, 
                            'filename' => $file_title
                        ]);
                }
            }
            else if($action == "Copy") {
                if(File::copy(storage_path('app/').$filePath['url'], storage_path('app/uploads/').$user_unique_id.'/'.($destPath != "" ? $destPath.'/': '').$file_title)) {
                    $unique_id = $filePath['unique_id'];
                    $file = FileModel::where('unique_id', $unique_id)->first();
                    
                    $newFile = $file->replicate();
                    $short_id = gen_uid(8);
                    $unique_id = Str::uuid()->toString();
                    $newFile->short_id = $short_id;
                    $newFile->unique_id = $unique_id;
                    $newFile->save();
                    FileModel::where('unique_id', $unique_id)
                        ->update([
                            'url' => 'uploads/'.$user_unique_id.'/'.($destPath != "" ? $destPath.'/': '').$file_title, 
                            'folder_path' =>'uploads/'.$user_unique_id.'/'.($destPath != "" ? $destPath.'/': ''),
                            'title' => $file_title,
                            'filename' => $file_title
                        ]);
                }
            }
            
        }
        return true;
    }
    public function editFilePrivacy(Request $request) {
        $file_unique_id = $request->input('unique_id');
        $is_protected = $request->input('is_protected');
        $password = $request->input('password');
        FileModel::where('unique_id', $file_unique_id)
            ->update([
                'is_protected' => $is_protected,
                'password' => $password
            ]);
        return true;
    }
    public function renameFile(Request $request) {
        
    }
    public function deleteFile(Request $request) {

    }
    
}
