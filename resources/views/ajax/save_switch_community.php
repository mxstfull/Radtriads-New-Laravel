<?php
include("../includes/config.php");
include("../includes/db_connect.php");
include("../includes/upload.class.php");

$result = array();

if(isset($_POST["photo_id"])) {
	
	if(!$_SESSION) {
		
		$result["error"] = "Oops. You are not logged in anymore. Please log-in again.";
		
	} else {
		
		$user_id = $_SESSION["USER_ID"];
		$photo_id = $_POST["photo_id"];
		
		// Get all the photos of this user
		$photos_query = $dbh->prepare("SELECT id, in_community FROM photo WHERE user_id = :user_id AND id = :photo_id");
		$photos_query->bindParam(":user_id", $user_id);
		$photos_query->bindParam(":photo_id", $photo_id);
		$photos_query->execute();	
		
		if($photos_query > 0) {
			
			$photo = $photos_query->fetch();
			
			if($photo["in_community"] == 0) {
				$new_in_community = 1;
			} else {
				$new_in_community = 0;
			}
			
			$photos_query = $dbh->prepare("UPDATE photo SET in_community = :new_in_community WHERE id = :photo_id");
			$photos_query->bindParam(":new_in_community", $new_in_community);
			$photos_query->bindParam(":photo_id", $photo_id);
			$photos_query->execute();	
			
			$result["new_in_community"] = $new_in_community;
			$result["status"] = 1;
			
		} else {
			
			$result["error"] = "Couldn't find this photo...";

		}
		
	}
	
} else {
	
	$result["error"] = "Oops";
	
}

echo json_encode($result);
?>