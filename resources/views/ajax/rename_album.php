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

if(isset($_POST["album_path"]) && isset($_POST["album_title"]) && isset($_POST["album_type"])) {

	$album_title = htmlspecialchars(trim($_POST["album_title"]));
	$album_type = htmlspecialchars($_POST["album_type"]);
	
	// Get the old album path
	$album_path = htmlspecialchars($_POST["album_path"]);
		
	if(empty($album_title)) {
		
		$result["error"] = "Your album title should not be empty...";
		
	} else {
				
		// Get infos about the current user...
		$user_infos = $dbh->prepare("SELECT u.id, u.unique_id, u.stripe_plan, p.diskspace AS max_diskspace, p.bandwidth AS max_bandwidth FROM user u, plan p WHERE u.id = :user_id AND u.plan_id = p.id");
		$user_infos->bindParam(":user_id", $user_id);
		$user_infos->execute();
		
		if($user_infos->rowCount() == 0) {
			
			$result["error"] = "There is an issue with your profile. Please try again later.";
			
		} else {
			
			if($album_type == "normal") {
			
				// Get infos about the current user...
				$new_album_name = $dbh->prepare("UPDATE album SET title = :album_title WHERE user_id = :user_id AND path = :path");
				$new_album_name->bindParam(":user_id", $user_id);
				$new_album_name->bindParam(":path", $album_path);
				$new_album_name->bindParam(":album_title", $album_title);
				$new_album_name->execute();
			
			} else {
				
				// Get infos about the current user...
				$new_album_name = $dbh->prepare("UPDATE album_home SET title = :album_title WHERE user_id = :user_id");
				$new_album_name->bindParam(":user_id", $user_id);
				$new_album_name->bindParam(":album_title", $album_title);
				$new_album_name->execute();
				
			} 
			
			$result["status"] = 1;
		
		}
		
	}
	
}

echo json_encode($result);