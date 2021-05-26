<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SessionModel extends Model
{
    //
    protected $fillable = [
        'user_id'
    ];
    protected $table = 'session';
    public $timestamps = true; 
}
