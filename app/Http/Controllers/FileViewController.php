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
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Filesystem\Filesystem;

class FileViewController extends Controller
{
    //
    public function __construct() {
        
    }
    public $filterArray = array (
        'Photo' => array('jpg', 'jpeg', 'png', 'git', 'tif'),
        'Music' => array('mp3', 'wav', 'wma'),
        'Video' => array('mp4', 'mov', 'swf', 'flv'),
        'Code' => array('txt', 'rtf', 'html', 'html5', 'webm', 'php', 'css', 'xml', 'json', 'pdf', 'docx', 'xlsx', 'pptx', 'java', 'rar', 'zip')
    );
    public function getFileByCategory(Request $request)
    {
        $searchText = $request->input('searchText');
        $unique_id = $request->input('unique_id');
        $user_id = $request->input('user_id');
        $currentPath = $request->input('currentPath');
        $category = $request->input('category');
        $categoryArray = ['Photo', 'Music', 'Video', 'Code'];
        if($currentPath == "home") $currentPath = $categoryArray[$category];
        if(!empty($currentPath)) $currentPath = $currentPath.'/';
        
        // $this->filterArray[$category]
        
        
        // $folderPath = str_replace(' ', '%20', $folderPath);
        if($category == -2) { //This is for deleted medias.
            //This is for erasing timeout medias.

            $junk_files = FileModel::where('updated_at', '<', Carbon::now()->subDays(14))
                ->where('is_deleted', 1)->get();
            foreach ($junk_files as $file) {
                if(File::delete(storage_path('app/').$file['url'])) {
                    $file->delete();
                    File::delete(storage_path('app/').$file['thumb_url']);
                }
            }

            $result = FileModel::select('unique_id', 'title', 'url', 'thumb_url', 'filename', 'diskspace', 'category', 'is_protected', 'is_picture', 'ext', 'created_at', 'updated_at')
            ->where('user_id', $user_id)
            ->where('is_deleted', 1)
            ->where('title', 'LIKE', "%$searchText%")
            ->orderby('created_at', 'desc')
            
            ->get();
        }
        else if($category == -1) { //This is for all medias.
            $pageNumber = $request->input('pageNumber');
            $result = [
            'total' =>FileModel::select('unique_id', 'title', 'url', 'thumb_url', 'filename', 'diskspace', 'category', 'is_protected', 'is_picture', 'ext', 'created_at', 'updated_at')
                ->where('user_id', $user_id)
                ->where('is_deleted', 0)
                ->where('title', 'LIKE', "%$searchText%")
                ->orderby('created_at', 'desc')
                ->skip($pageNumber * 200)->take(200)
                ->get(),

            'recent'=> FileModel::select('unique_id', 'title', 'url', 'thumb_url', 'filename', 'diskspace', 'category', 'is_protected', 'is_picture', 'ext', 'created_at', 'updated_at')
                ->where('user_id', $user_id)
                ->where('is_deleted', 0)
                ->where('title', 'LIKE', "%$searchText%")
                ->orderby('created_at', 'desc')
                ->take(200)
                ->get()
            ];
        }
        else { //This is for specific category.
            
            $realPath = storage_path('app/uploads/').$unique_id."/".$currentPath; 
            $folderPath = 'uploads/'.$unique_id.'/'.$currentPath; 
            $result = FileModel::select('unique_id', 'title', 'url', 'thumb_url', 'filename', 'diskspace', 'category', 'is_protected', 'is_picture', 'ext', 'created_at', 'updated_at')
            ->where('folder_path', 'like', $folderPath)
            ->where('user_id', $user_id)
            ->where('is_deleted', 0)
            ->where('category', $category)
            ->where('title', 'LIKE', "%$searchText%")
            ->orderby('created_at', 'desc')
            ->get();
        }
        
        return response()->json($result);
    }
    public function downloadFiles(Request $request) {
        
        $fileList = $request->input('fileList');
        $public_dir=public_path();
        
        if(count($fileList) == 1) {
            $filepath = storage_path('App/').$fileList[0]["url"];
            return Response::download($filepath);
        }
        else {
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
                            // 'title' => $file_title, 
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
                    $unique_id = sha1(time().mt_rand(0,9999));
                    $newFile->short_id = $short_id;
                    $newFile->unique_id = $unique_id;
                    $newFile->save();
                    FileModel::where('unique_id', $unique_id)
                        ->update([
                            'url' => 'uploads/'.$user_unique_id.'/'.($destPath != "" ? $destPath.'/': '').$file_title, 
                            'folder_path' =>'uploads/'.$user_unique_id.'/'.($destPath != "" ? $destPath.'/': ''),
                            // 'title' => $file_title,
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
        $fileItem = $request->input('item');
        $newFileName = $request->input('newFileName');
        $unique_id = $fileItem['unique_id'];
        $extension = FileModel::where('unique_id', $unique_id)
            ->first()->ext;
        FileModel::where('unique_id', $unique_id)
            ->update([
                'title' => $newFileName.'.'.$extension,
            ]);
        return response()->json(['newFileName' => $newFileName.'.'.$extension],200);

    }
    public function deleteFile(Request $request) {
        $fileItems = $request->input('item');
        foreach($fileItems as $fileItem)
        {
            $file_unique_id = $fileItem['unique_id'];
            FileModel::where('unique_id', $file_unique_id)
            ->update([
                'is_deleted' => 1
            ]);
        }
        return true;
    }
    public function renameAlbum(Request $request) {
        $currentPath = $request->input('current_path');
        $unique_id = $request->input('unique_id');
        $newAlbumName = $request->input('newAlbumName');
        $user_id = $request->input('user_id');
        if($currentPath == "home") {
            return true;
        }
        else {
            $currentPath = "uploads/".$unique_id."/".$currentPath."/";
        }
        return AlbumModel::where('path', $currentPath)
        ->where('user_id', $user_id)
        ->update([
            'title' => $newAlbumName
        ]);
    }
    public function recoverFiles(Request $request) {
        $fileItems = $request->input('item');
        foreach($fileItems as $fileItem)
        {
            $file_unique_id = $fileItem['unique_id'];
            FileModel::where('unique_id', $file_unique_id)
            ->update([
                'is_deleted' => 0
            ]);
        }
        return true;
    }
    public function permanentlyDeleteFiles(Request $request) {
        $fileItems = $request->input('item');
        foreach($fileItems as $fileItem)
        {
            $file_unique_id = $fileItem['unique_id'];
            if(File::delete(storage_path('app/').$fileItem['url'])) {
                FileModel::where('unique_id', $file_unique_id)
                ->delete();
                File::delete(storage_path('app/').$fileItem['thumb_url']);
            }
            
        }
    }
    public function getItemByUniqueId(Request $request) {
        $unique_id = $request->input('unique_id');
        if($unique_id == null) return false;
        else {
            $result = FileModel::select('unique_id', 'title', 'url', 'thumb_url', 'filename', 'diskspace', 'category', 'is_protected', 'is_picture', 'ext', 'created_at', 'updated_at', 'user_id', 'password')
                    ->where('unique_id', $unique_id)
                    ->where('category', 0)
                    ->first();
            return response()->json($result);
        }
    }
    public function deleteAlbum(Request $request) {
        $unique_id = $request->input('unique_id');
        $currentPath = $request->input('currentPath');
        $user_id = $request->input('user_id');
        // $category = $request->input('category');
        //delete all files in current album
        if(File::isDirectory(storage_path('app/uploads/'.$unique_id.'/'.$currentPath)))
            File::deleteDirectory(storage_path('app/uploads/'.$unique_id.'/'.$currentPath));
        FileModel::select('id')
                ->where('folder_path', 'like', "%uploads/".$unique_id."/".$currentPath."%")
                ->delete();
        AlbumModel::select('id')
                ->where('path', 'like', "%uploads/".$unique_id."/".$currentPath."%")
                ->where('user_id', $user_id)
                ->delete();
        
    }
}
