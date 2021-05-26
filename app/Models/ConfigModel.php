<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConfigModel extends Model
{
    //

    protected $fillable = [
        'config_name', 'config_value'
    ];
    protected $table = 'config';
    public $timestamps = true; 
}
