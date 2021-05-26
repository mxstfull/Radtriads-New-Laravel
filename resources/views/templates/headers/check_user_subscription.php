<?php
if($_SESSION) {
	
	$user_id = $_SESSION["USER_ID"];
	$is_free_trial = false;
	
	$user_subscription_infos = $dbh->prepare("SELECT * FROM user WHERE id = :user_id");
	$user_subscription_infos->bindParam(":user_id", $user_id);
	$user_subscription_infos->execute();
	
	$user_subscription = $user_subscription_infos->fetch();
	
	$user_created_at = $user_subscription["created_at"];
	$user_created_at_time = strtotime($user_created_at);
	$user_has_seen_10_days_left_popup = $user_subscription["has_seen_10_days_left_popup"];
	$show_10_days_left_popup = 0;
	$plan_selected = $user_subscription["stripe_plan"];
	$first_pay = $user_subscription["first_pay"];
	$stripe_plan_admin = $user_subscription["stripe_plan_admin"];
	
	$sub_admin_set = false;
	
	if($stripe_plan_admin != NULL) {
		
		$sub_admin_set = true;
		
		if($stripe_plan_admin == "silver") {
			$plan_selected = STRIPE_PLAN_1;
		} else if($stripe_plan_admin == "gold") {
			$plan_selected = STRIPE_PLAN_5;
		} else if($stripe_plan_admin == "platinum") {
			$plan_selected = STRIPE_PLAN_6;
		}
		
	} else {
	
		// Is the user on free trial?
		if($user_created_at_time > strtotime("-30 days") && $first_pay == 0) {
			
			$is_free_trial = true;
			
			// -- Check if the user should see the 10 days left popup
			// Determine the end date of the free trial
			$end_free_trial_date = strtotime('+30 days', $user_created_at_time);
			$datediff = $end_free_trial_date - time();
			
			// We get the number of days before the trial ends
			$days_left_before_trial_ends = round($datediff / (60 * 60 * 24));
			
			if($user_has_seen_10_days_left_popup == 0 && ($days_left_before_trial_ends > 0 && $days_left_before_trial_ends <= 10)) {
				
				$show_10_days_left_popup = 1;
				
			}
			
			
		} 
		// The user is either on a paid plan OR is over the 30 days free trial
		else {
			
			// The user is paying, check his subscription with Stripe
			if($first_pay == 1) {
				
				\Stripe\Stripe::setApiKey(STRIPE_SECRET_KEY);
				
				$user_stripe_sub = $user_subscription["stripe_subscription_id"];
				
				
				try {
					
					$stripe_sub = \Stripe\Subscription::retrieve(
					  $user_stripe_sub,
					  []
					);
				
					if($stripe_sub->status != "active") {
						header("Location: switch-plan.php?action=need_renew_not_active");
						exit;
					}
				
				} catch(Exception $e) {
					header("Location: switch-plan.php?action=need_renew_not_active");
					exit;
				}
				
			} else {
				
				header("Location: switch-plan.php?action=need_renew");
				exit;
				
			}
			
		}
	
	}
	
	// Determine the displayed plan name
	if($plan_selected == "silver_monthly" || $plan_selected == "silver_yearly") {
		$displayed_plan_name = "Silver";
	} 
	else if($plan_selected == "gold_monthly" || $plan_selected == "gold_yearly") 
	{
		$displayed_plan_name = "Gold";
	} 
	else if($plan_selected == "platinum_monthly" || $plan_selected == "platinum_yearly") 
	{
		$displayed_plan_name = "Platinum";
	}
	
	// Silver plan
	if($plan_selected == STRIPE_PLAN_4 || $plan_selected == STRIPE_PLAN_1) {
		$displayed_plan_name = "Silver";
	} 
	
	// Gold plan
	if($plan_selected == STRIPE_PLAN_2 || $plan_selected == STRIPE_PLAN_5) {
		$displayed_plan_name = "Gold";		
	} 
	
	// Platinum plan
	if($plan_selected == STRIPE_PLAN_3 || $plan_selected == STRIPE_PLAN_6) {
		$displayed_plan_name = "Platinum";		
	} 
	
}	

