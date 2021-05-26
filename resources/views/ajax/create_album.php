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

if(isset($_POST["album_path"]) && isset($_POST["album_title"])) {

	$album_title = htmlspecialchars(trim($_POST["album_title"]));
	$album_path = htmlspecialchars($_POST["album_path"]);
	
	if(empty($album_title)) {
		
		$result["error"] = "Your album title should not be empty...";
		
	} else if(empty($album_path)) {
		
		$result["error"] = "Please select the album path...";
		
	} else if (preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $album_title)) {
	
	 	$result["error"] = "Please don't use any special character in your album title...";
		
	} else {
		
		// Get infos about the current user...
		$user_infos = $dbh->prepare("SELECT u.id, u.unique_id, u.stripe_plan, p.diskspace AS max_diskspace, p.bandwidth AS max_bandwidth FROM user u, plan p WHERE u.id = :user_id AND u.plan_id = p.id");
		$user_infos->bindParam(":user_id", $user_id);
		$user_infos->execute();
		
		if($user_infos->rowCount() == 0) {
			
			$result["error"] = "There is an issue with your profile. Please try again later.";
			
		} else {
		
			$user = $user_infos->fetch();
			$new_album_path = "../".$album_path . $album_title . "/";
			
			// This directory doesn't exist, let's create it!
			if (!is_dir($new_album_path)) {
				
				mkdir($new_album_path);
				
				$sql_path = str_replace("../", "", $new_album_path);
				
				$album_query = $dbh->prepare("INSERT INTO album SET created_at = NOW(), updated_at = NOW(), title = :title, path = :path, user_id = :user_id");
				$album_query->bindParam(":user_id", $user_id);
				$album_query->bindParam(":title", $album_title);
				$album_query->bindParam(":path", $sql_path);
				$album_query->execute();
				
				$result["success"] = 1;
				
			} else {
				
				$result["error"] = "This album already exists here, please try another album title.";
				
			}
		
		}
		
	}
	
}

echo json_encode($result);