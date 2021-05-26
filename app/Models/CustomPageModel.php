<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomPageModel extends Model
{
    //

    protected $fillable = [
        'id', 'title', 'content', 'user_id', 'date', 'allow_date', 'status'
    ];
    protected $table = 'custom_page';
    public $timestamps = true; 
}
