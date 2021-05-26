<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CustomPageModel;

class CustomPageController extends Controller
{
    //
    public function getPageById(Request $request) {
        $page_id = $request->input('page_id');
        $result = CustomPageModel::select('title', 'content')
            ->where('id' , $page_id)
            ->get()->first();
        return response()->json($result);
    }
}
