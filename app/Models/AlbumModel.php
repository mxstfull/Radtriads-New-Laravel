<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AlbumModel extends Model
{
    //

    protected $fillable = [
        'user_id', 'title', 'path'
    ];
    protected $table = 'album';
    public $timestamps = true; 
    
}
