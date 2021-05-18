<?php
/**
 * Check if user's plan is expired
 *
 *
 * @return \Illuminate\Http\JsonResponse
 */
function checkUserPlan($userId) {
    $result = [];
    $result['is_free_trial'] = false;

    $user_subscription = DB::table('user')->where('id', $userId)->first();
    
    $result['user_should_renew'] = NULL;
    $user_created_at = $user_subscription->created_at;
    $user_created_at_time = strtotime($user_created_at);
    $user_has_seen_10_days_left_popup = $user_subscription->has_seen_10_days_left_popup;
    $result['show_10_days_left_popup'] = 0;
    $result['plan_selected'] = $user_subscription->stripe_plan;
    $first_pay = $user_subscription->first_pay;
    $stripe_plan_admin = $user_subscription->stripe_plan_admin;
    
    $result['sub_admin_set'] = false;
    
    if($stripe_plan_admin != NULL) {
        
        $result['sub_admin_set'] = true;
        
        if($stripe_plan_admin == "silver") {
            $result['plan_selected'] = config('constants.STRIPE_PLAN_1');
        } else if($stripe_plan_admin == "gold") {
            $result['plan_selected'] = config('constants.STRIPE_PLAN_5');
        } else if($stripe_plan_admin == "platinum") {
            $result['plan_selected'] = config('constants.STRIPE_PLAN_6');
        }
        
    } else {
    
        // Is the user on free trial?
        if($user_created_at_time > strtotime("-30 days") && $first_pay == 0) {
            
            $result['is_free_trial'] = true;
            
            // -- Check if the user should see the 10 days left popup
            // Determine the end date of the free trial
            $end_free_trial_date = strtotime('+30 days', $user_created_at_time);
            $datediff = $end_free_trial_date - time();
            
            // We get the number of days before the trial ends
            $result['days_left_before_trial_ends'] = round($datediff / (60 * 60 * 24));
            $result['trial_ends_date'] = $end_free_trial_date;
            
            if($user_has_seen_10_days_left_popup == 0 && ($result['days_left_before_trial_ends'] > 0 && $result['days_left_before_trial_ends'] <= 10)) {
                $result['show_10_days_left_popup'] = 1;
            }
            
            
        } 
        // The user is either on a paid plan OR is over the 30 days free trial
        else {
            
            // The user is paying, check his subscription with Stripe
            if($first_pay == 1) {
                
                \Stripe\Stripe::setApiKey(config('constants.STRIPE_SECRET_KEY'));
                
                $user_stripe_sub = $user_subscription->stripe_subscription_id;
                
                
                try {
                    
                    $stripe_sub = \Stripe\Subscription::retrieve($user_stripe_sub,[]);
                
                    if($stripe_sub->status != "active") {
                        $result['user_should_renew'] = "need_renew_not_active";
                    }
                
                } catch(Exception $e) {
                    $result['user_should_renew'] = "need_renew_not_active";
                }
                
            } else {
                $result['user_should_renew'] = "need_renew";                    
            }   
        }
    }
    
    // Determine the displayed plan name
    if($result['plan_selected'] == "silver_monthly" || $result['plan_selected'] == "silver_yearly") {
        $result['displayed_plan_name'] = "Silver";
    } 
    else if($result['plan_selected'] == "gold_monthly" || $result['plan_selected'] == "gold_yearly") 
    {
        $result['displayed_plan_name'] = "Gold";
    } 
    else if($result['plan_selected'] == "platinum_monthly" || $result['plan_selected'] == "platinum_yearly") 
    {
        $result['displayed_plan_name'] = "Platinum";
    }
    
    // Silver plan
    if($result['plan_selected'] == config('constants.STRIPE_PLAN_4') || $result['plan_selected'] == config('constants.STRIPE_PLAN_1')) {
        $result['displayed_plan_name'] = "Silver";
    } 
    
    // Gold plan
    if($result['plan_selected'] == config('constants.STRIPE_PLAN_2') || $result['plan_selected'] == config('constants.STRIPE_PLAN_5')) {
        $result['displayed_plan_name'] = "Gold";		
    } 
    
    // Platinum plan
    if($result['plan_selected'] == config('constants.STRIPE_PLAN_3') || $result['plan_selected'] == config('constants.STRIPE_PLAN_6')) {
        $result['displayed_plan_name'] = "Platinum";		
    }
    return $result;
}

function getStripeId($plan_id, $plan_method) {
    $stripe_id = '';
    if ($plan_id == 1) {
        if ($plan_method == 'monthly') {
            $stripe_id = config('constants.STRIPE_PLAN_1');
        }
        else {
            $stripe_id = config('constants.STRIPE_PLAN_4');
        }
    } else if ($plan_id == 2) {
        if ($plan_method == 'monthly') {
            $stripe_id = config('constants.STRIPE_PLAN_2');
        }
        else {
            $stripe_id = config('constants.STRIPE_PLAN_5');
        }
    } else if ($plan_id == 3) {
        if ($plan_method == 'monthly') {
            $stripe_id = config('constants.STRIPE_PLAN_3');
        }
        else {
            $stripe_id = config('constants.STRIPE_PLAN_6');
        }
    }
    return $stripe_id;
}