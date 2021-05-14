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

class PlanController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct() {
        $this->middleware('auth:api', ['except' => ['request_url', 'webhook']]);
    }

    public function request_url(Request $request) {
        $unique_id = $request['u_id'];
        $plan_id = $request['plan_id'];
        $plan_method = $request['plan_method'];
        $success_url = $request['success_url'];
        $cancel_url = $request['cancel_url'];
        $payment_id = getStripeId($plan_id, $plan_method);

        $user = DB::table('users')->where('unique_id', $unique_id)->first();
        
        \Stripe\Stripe::setApiKey(config('constants.STRIPE_SECRET_KEY'));

        $stripe_plan = \Stripe\Checkout\Session::create([
                'customer_email' => $user->email,
                'client_reference_id' => $user->unique_id,
                'success_url' => $success_url . '?plan=' . $payment_id . "&plan_id=" . $plan_id,
                'cancel_url' => $cancel_url,
                'payment_method_types' => ['card'],
                'metadata' => [
                    "plan_id" => $plan_id
                ],
                'subscription_data' => [
                    'items' => [
                        [
                            'plan' => $payment_id
                        ],
                    ],
                ],
        ]);

        return response()->json([
            'stripe_key' => config('constants.STRIPE_KEY'),
            'stripe_id' => $stripe_plan->id
        ]);
    }


    public function webhook(Request $request) {
        \Stripe\Stripe::setApiKey(config('constants.STRIPE_SECRET_KEY'));

        // You can find your endpoint's secret in your webhook settings
        $endpoint_secret = config('constants.STRIPE_WEBHOOK_KEY');

        $payload = @file_get_contents('php://input');
        $sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'];
        $event = null;

        try {
            $event = \Stripe\Webhook::constructEvent(
                $payload, $sig_header, $endpoint_secret
            );
        } catch(\UnexpectedValueException $e) {
            // Invalid payload
            return response('Invalid payload', 400); // PHP 5.4 or greater
        } catch(\Stripe\Error\SignatureVerification $e) {
            // Invalid signature
            return response('Invalid signature', 400); // PHP 5.4 or greater
        }

        // Handle the checkout.session.completed event
        if ($event->type == 'checkout.session.completed') {
            $session = $event->data->object;
        
            if($session) {
                
                $unique_id = $session->client_reference_id;
                
                // Check if the user already has a subscription active
                $user_subscription_i =  DB::table('users')->where('unique_id', $unique_id)->first();
                
                // Cancel the old subscription if we have one
                if($user_subscription_i["stripe_subscription_id"] != "") {
                    
                    $user_subscription_active = $user_subscription_i["stripe_subscription_id"];
                    
                    $active_stripe_subscription = \Stripe\Subscription::retrieve($user_subscription_active);
                    $active_stripe_subscription->cancel();
                    
                    echo "CANCELLING PLAN $user_subscription_active";
                    
                } else {
                    echo "OLD SUBSCRIPTION NOT FOUND";
                }
                
                $customer_id = $session->customer;
                $subscription_id = $session->subscription;
                $stripe_plan = $session->display_items[0]->plan->id;
                $plan_id = $session->metadata->plan_id;
                
                if($session->mode == "setup") {
                    
                    $setup_intent = $session->setup_intent;
                    $intent = \Stripe\SetupIntent::retrieve($setup_intent);

                    $subscription_id = $intent->metadata->subscription_id;
                    $customer_id = $intent->metadata->customer_id;
                    $pm = $intent->payment_method;
                    
                    $payment_method = \Stripe\PaymentMethod::retrieve(
                        $pm
                    );
                    
                    $payment_method->attach([
                        'customer' => $customer_id,
                    ]);
                    
                    $updated = \Stripe\Customer::update(
                    $customer_id,
                    [
                        'invoice_settings' => ['default_payment_method' => $payment_method],
                    ]
                    );
                    
                } else {
                    DB::table('users')
                        ->where('unique_id', $unique_id)
                        ->update([
                            'first_pay'=> 1,
                            'stripe_subscription_id' => $subscription_id,
                            'stripe_customer_id' => $customer_id,
                            'plan_id' => $plan_id,
                            'stripe_plan' => $stripe_plan,
                            'stripe_plan_admin' => NULL,
                            'first_pay_admin' => 0
                        ]);
                }
            }
            
        }
        return response('Success', 200); // PHP 5.4 or greater
    }

}
