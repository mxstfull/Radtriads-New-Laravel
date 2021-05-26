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
use App\Models\SessionModel;

class AuthController extends Controller {

    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct() {
        $this->middleware('auth:api', ['except' => ['login', 'register', 'verifyUser', 'logout']]);
    }

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request){

        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        if (! $token = auth()->attempt($validator->validated())) {
            return response()->json(['error' => 'Either email or password is wrong.'], 401);
        }
        $response = $this->createNewToken($token);
        $response['userPlan'] = checkUserPlan(auth()->user()['id']);
        SessionModel::where('id', '>',  0)
            ->delete();
        SessionModel::create( array ('user_id' => auth()->user()['id']) );
        return response()->json($response);
    }

    /**
     * Register a User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request) {
        $validator = Validator::make($request->all(), [
            'username' => 'required|string|between:2,100',
            'email' => 'required|string|email|max:100|unique:user',
            'password' => 'required|string|confirmed|min:6',
            'acceptTerms' => 'accepted',
        ]);

        if($validator->fails()){
            return response()->json($validator->errors(), 400);
        }

        $plan_selected = $request->input('planOption');
        if(	$plan_selected != "silver_monthly" && 
					$plan_selected != "gold_monthly" && 
					$plan_selected != "platinum_monthly" && 
					$plan_selected != "silver_yearly" &&
					$plan_selected != "gold_yearly" &&
					$plan_selected != "platinum_yearly")
        {
            return response()->json("no_plan_selected", 400);
        }
        // Determine the plan ID
        $plan_id = 4;
        if($plan_selected == "silver_monthly" || $plan_selected == "silver_yearly") {
            $plan_id = 1;
        } 
        else if($plan_selected == "gold_monthly" || $plan_selected == "gold_yearly") 
        {
            $plan_id = 2;
        } 
        else if($plan_selected == "platinum_monthly" || $plan_selected == "platinum_yearly") 
        {
            $plan_id = 3;
        }
        ///
        $verification_code = Str::uuid()->toString();
        $rank;
        $row_count = User::select('id')->get()
            ->count();
        if($row_count > 0) $rank = 0;
        else $rank = 1;
        $user = User::create(array_merge(
                    $validator->validated(),
                    [
                        'password' => bcrypt($request->password),
                        'unique_id' => Str::uuid()->toString(),
                        'email_activation_code' => $verification_code,
                        'stripe_plan' => $plan_selected, 
                        'plan_id' => $plan_id,
                        'profile_picture' => '',
                        'rank' => $rank,
                        'status' => 1
                    ]
                ));

        // $name = $request->name;
        // $email = $request->email;        
        // $subject = "Please verify your email address.";
        // Mail::send('email.verify', ['name' => $name, 'verification_code' => $verification_code],
        //     function($mail) use ($email, $name, $subject){
        //         $mail->from(getenv('FROM_EMAIL_ADDRESS'), "From User/Company Name Goes Here");
        //         $mail->to($email, $name);
        //         $mail->subject($subject);
        //     });


        return response()->json([
            'message' => 'User successfully registered',
            'verification' => $verification_code,
            'user' => $user
        ], 201);
    }
    /**
     * API Verify User
     *
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function verifyUser(Request $request)
    {

        $check = DB::table('user')->where('email_activation_code',$request['vcode'])->first();

        if(!is_null($check)){
            $user = User::find($check->id);

            if($user->email_verified == 1){
                return response()->json([
                    'success'=> true,
                    'message'=> 'Account already verified..'
                ]);
            }
 
            $user->update(['email_verified' => 1]);

//            DB::table('user')->where('email_activation_code',$verification_code)->delete();

            return response()->json([
                'success'=> true,
                'message'=> 'You have successfully verified your email address.'
            ]);
        }
        
        return response()->json(['error' => 'Reset your verification code. Not correct!'], 401);

    }
 
    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout() {
        if(auth()->user())
            auth()->logout();
        return true;
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh() {
        return response()->json($this->createNewToken(auth()->refresh()));
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function userProfile() {
        return response()->json(auth()->user());
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function createNewToken($token){
        return [
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60,
            'user' => auth()->user()
        ];
    }
}
