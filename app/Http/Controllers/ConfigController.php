<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Response;
use App\Models\ConfigModel;


class ConfigController extends Controller
{
    //
    public function getLogoUrl() {
        $result = ConfigModel::select('config_value')
            ->where('config_name' , 'website_logo')
            ->get()->first();
        return response()->json($result);
    }
}
