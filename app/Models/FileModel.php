<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FileModel extends Model
{
    //
    protected $fillable = [
        'short_id', 'title', 'unique_id', 'url', 'folder_path', 'filename', 'ext', 'diskspace', 'bandwidth', 'ip_address', 'user_id', 'is_picture', 'category', 'is_protected'
    ];
    protected $table = 'file';
    public $timestamps = true; 
}
