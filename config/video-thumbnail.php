<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Binaries
    |--------------------------------------------------------------------------
    |
    | Paths to ffmpeg nad ffprobe binaries
    |
    */

    'binaries' => [
        'ffmpeg'  => env('FFMPEG', 'C:\Program Files (x86)\ffmpeg\bin\ffmpeg'),
        'ffprobe' => env('FFPROBE', 'C:\Program Files (x86)\ffmpeg\bin\ffprobe')
    ]
];