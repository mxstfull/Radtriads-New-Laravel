<?php
$limit_diskspace_reached = 0;
$limit_nb_files_reached = 0;
	
if($_SESSION) {
	
	$user_id = $_SESSION["USER_ID"];
	
	$user_infos = $dbh->prepare("SELECT u.id, u.unique_id, u.stripe_plan, p.diskspace AS max_diskspace, p.bandwidth AS max_bandwidth, p.images AS max_files, show_social_share, show_direct_link, show_forum_code, show_html_code FROM user u, plan p WHERE u.id = :user_id AND u.plan_id = p.id");
	$user_infos->bindParam(":user_id", $user_id);
	$user_infos->execute();
	
	$user = $user_infos->fetch();
	$max_diskspace = $user["max_diskspace"]*100000;
	$max_bandwidth = $user["max_bandwidth"]*100000;
	$max_files = $user["max_files"];
	
	// Count the number of files
	$user_files = $dbh->prepare("SELECT id FROM file WHERE user_id = :user_id AND is_deleted = 0");
	$user_files->bindParam(":user_id", $user_id);
	$user_files->execute();
	
	$nb_user_files = $user_files->rowCount();
	
		
	// Get total bandwidth used
	$user_usage_used = $dbh->prepare("SELECT 
									  SUM(bandwidth) AS month_bandwidth,
									  SUM(diskspace) AS month_diskspace
									  FROM file 
									  WHERE 
									  user_id = :user_id");
	$user_usage_used->bindParam(":user_id", $user_id);
	$user_usage_used->execute();
										  
	if($user_usage_used->rowCount() > 0) {
				
		$user_usage = $user_usage_used->fetch();

		$user_diskspace = $user_usage["month_diskspace"];
		$user_bandwidth = $user_usage["month_bandwidth"];
		
			
	} else {
		
		$user_diskspace = 0;
		$user_bandwidth = 0;
		
	}	
	
	if($max_diskspace == 0) {
		$percent_diskspace = INF;
	} else {
		$percent_diskspace = ($user_diskspace / $max_diskspace) * 100;
		$percent_diskspace = round($percent_diskspace, 2);
	}
	
	if($max_bandwidth == 0) {
		$percent_bandwidth = INF;
	} else {
		$percent_bandwidth = ($user_bandwidth / $max_bandwidth) * 100;
	}
		
	if(is_nan($percent_diskspace)) { 
		$percent_diskspace = INF;
	}
	
	if($percent_diskspace > 100 && !is_infinite($percent_diskspace)) {
		$percent_diskspace = 100;
		$limit_diskspace_reached = 1;
	}
	
	if($nb_user_files >= $max_files && $max_files != 0) {
		$limit_nb_files_reached = 1;
	}
	
	
}
?>