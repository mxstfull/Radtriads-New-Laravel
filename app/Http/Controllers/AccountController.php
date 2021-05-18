<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Validator;
use App\User;
use Illuminate\Support\Str;

use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use DB, Hash, Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Mail\Message;
use App\Models\FileModel;
use Illuminate\Support\Facades\File;

class AccountController extends Controller {

    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct() {
        $this->middleware('auth:api', ['except' => ['MyInfo', 'GetUserData' , 'Settings' , 'Privacy' , 'delete', 'getDiskUsage', 'uploadAvatar']]);
    }

        /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */ 
    public function GetUserData(Request $request){
        $unique_id = $request['u_id'];
        $user = DB::table('user')->where('unique_id',$request['u_id'])->first();
        $plan_detail = DB::table('plan')->where('id', $user->plan_id)->first();

        $check = [
            'user' => $user,
            'plan' => checkUserPlan($user->id)
        ];

        $check['plan']['detail'] = $plan_detail;

        return response()->json([
            'success'=> true,
            'message'=> $check
        ]);
    }

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function MyInfo(Request $request){

        $data = $request['value'];
        $unique_id = $request['val'];

        $validator = Validator::make($data, [
            'old_password' => 'required|string|min:6',
            'new_password' => 'required|string|min:6',
               'new_password_confirmation' => 'required|string|min:6',
        ]);
        
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

//        $check = DB::table('user')->where('unique_id',$unique_id['u_id'])->first();

        $data_q=array('password' => $data['new_password'] , 'unique_id'=>$unique_id['u_id']);
        $result =  DB::update('update user set password = ? where unique_id = ?',[bcrypt($data['new_password']),$unique_id['u_id']]);
        return response()->json([
            'success'=> true,
            'message'=> $result
        ]);
    }
    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */ 
    public function Settings(Request $request){
        $data = $request['value'];
        $unique_id = $request['val'];
        $result =  DB::update('update user set show_direct_link = ? where unique_id = ?',[$data['check_direct'] ,$unique_id['u_id']]);
        $result =  DB::update('update user set show_html_code = ? where unique_id = ?',[$data['check_html'] ,$unique_id['u_id']]);
        $result =  DB::update('update user set show_forum_code = ? where unique_id = ?',[$data['check_bulletin'] ,$unique_id['u_id']]);
        $result =  DB::update('update user set show_social_share = ? where unique_id = ?',[$data['check_button'] ,$unique_id['u_id']]);
        $result = User::select('show_direct_link', 'show_html_code', 'show_forum_code', 'show_social_share')
            ->where('unique_id', $unique_id)
            ->get()->first();
        return response()->json([
            'success'=> true,
            'message'=> $result
        ]);
    }
    public function Privacy(Request $request){
        $data = $request['value'];
        $unique_id = $request['val'];
        $result =  DB::update('update user set is_account_public = ? where unique_id = ?',[$data['seleted'] ,$unique_id['u_id']]);
        return response()->json([
            'success'=> true,
            'message'=> $result
        ]);
    }
    public function delete(Request $request){
        $result = DB::delete('delete from user where unique_id = ?',[$request['u_id']]);
        return response()->json([
            'success'=> true,
            'message'=> $result
        ]);
    }
    public function getDiskUsage(Request $request) {
        $user_id = $request->input("user_id");
        $max_space =DB::table('user')
            ->where('user.id', $user_id)
            ->join('plan', 'plan.id', '=', 'user.plan_id')
            ->select('plan.diskspace')
            ->get()->first();
        $diskUsage_all = FileModel::select(DB::raw("sum(diskspace) as diskspace"))
            ->where('user_id', $user_id)
            ->where('is_deleted', 0)
            ->get()->first();
        $diskUsage_category = FileModel::select("category", DB::raw("sum(diskspace) as diskspace"))
            ->where('user_id', $user_id)
            ->where('is_deleted', 0)
            ->groupBy('category')
            ->get();
        $diskUsage_deleted = FileModel::select(DB::raw("sum(diskspace) as diskspace"))
            ->where('user_id', $user_id)
            ->where('is_deleted', 1)
            ->get()->first();
        $file_count = FileModel::select(DB::raw('count(id) as count'))
            ->where('user_id', $user_id)
            ->get()->first()->count;
        return response()->json([
            'all' => (($max_space)?$max_space->diskspace:0) * 1000 * 1000,
            'used_all' => ($diskUsage_all)?$diskUsage_all->diskspace:0,
            'category' => $diskUsage_category,
            'deleted' => ($diskUsage_deleted)?$diskUsage_deleted->diskspace:0,
            'file_count' => $file_count
        ]);
    }
    public function uploadAvatar(Request $request) {
        $file = $request->file('image');
        $unique_id = $request->unique_id;
        $filename  = $file->getClientOriginalName();
        $extension = $file->getClientOriginalExtension();
        $avatarFileName = $unique_id.'.'.$extension;
        if(!is_dir(storage_path('app/avatars')))
            File::makeDirectory(storage_path('app/avatars'));
        $file->move(storage_path('app/avatars'), $avatarFileName);
        User::where('unique_id', $unique_id)
            ->update(['profile_picture' => $avatarFileName]);
    }
}
