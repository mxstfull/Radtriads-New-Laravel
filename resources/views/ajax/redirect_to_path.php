<?php
include("../includes/config.php");
include("../includes/functions.php");
include("../includes/db_connect.php");
include("../includes/upload.class.php");

$result = array();

if(!$_SESSION) {
	exit;
}

$user_id = $_SESSION["USER_ID"];
$is_picture = 0;
$result = array();

if(isset($_POST["selected_path"])) {

	$selected_path = htmlspecialchars($_POST["selected_path"]);	
	
	if(empty($selected_path)) {
		
		$result["error"] = "Please select the album path...";
		 
	} else {
		
		// Get infos about the current user...
		$user_infos = $dbh->prepare("SELECT u.id, u.unique_id, u.stripe_plan, p.diskspace AS max_diskspace, p.bandwidth AS max_bandwidth FROM user u, plan p WHERE u.id = :user_id AND u.plan_id = p.id");
		$user_infos->bindParam(":user_id", $user_id);
		$user_infos->execute();
		
		if($user_infos->rowCount() == 0) {
			
			$result["error"] = "There is an issue with your profile. Please try again later.";
			
		} else {
			
			$user = $user_infos->fetch();
			$user_unique_id = $user["unique_id"];
			
			if(strpos($selected_path, $user_unique_id) !== false) {
				
				$result["success"] = true;
				
			} else {
				
				$result["error"] = "Not allowed to go in this album...";
								
			}
							
		}
		
	}
	
}

echo json_encode($result);