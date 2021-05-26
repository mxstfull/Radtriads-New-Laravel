<?php
include("../includes/config.php");
include("../includes/db_connect.php");
include("../includes/upload.class.php");

$result = array();

if(isset($_POST["last_photo_id"]) && isset($_POST["profile_id"])) {
	
	$profile_id = $_POST["profile_id"];
	
	if(!$_SESSION) {
		
		$result["error"] = "Oops. You are not logged in anymore. Please log-in again.";
		
	} else if($_SESSION["RANK"] < 1) {
	
			$result["status"] = 0;
			$result["error"] = "You're not an admin";
			
	} else {
		
		$last_photo_id = $_POST["last_photo_id"];
		
		// Get all the photos of this user
		$photos_query = $dbh->prepare("SELECT * FROM file WHERE id < :last_photo_id AND user_id = :user_id ORDER BY id DESC LIMIT 10");
		$photos_query->bindParam(":last_photo_id", $last_photo_id);
		$photos_query->bindParam(":user_id", $profile_id);
		$photos_query->execute();	
		
		$result["photos"] = $photos_query->fetchAll();
		$result["status"] = 1;
	
		
	}
	
} else {
	
	$result["error"] = "Oops";
	
}

echo json_encode($result);
?>