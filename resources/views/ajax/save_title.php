<?php
include("../includes/config.php");
include("../includes/db_connect.php");
include("../includes/upload.class.php");

$result = array();

if(isset($_POST["photo_id"]) && isset($_POST["title"])) {
	
	$is_owner = false;

	$photo_id = $_POST["photo_id"];
	$title = $_POST["title"];
	
	if(TEST_MODE) {
		$result["error"] = "You can't update the title in demo mode.";
	} else {
	
		// Get all the photos of this user
		$photos_query = $dbh->prepare("SELECT id, user_id FROM photo WHERE id = :photo_id");
		$photos_query->bindParam(":photo_id", $photo_id);
		$photos_query->execute();	
			
		if($photos_query->rowCount() > 0) {
			
			$photo = $photos_query->fetch();
					
			if($_SESSION) {
								
				if($_SESSION["USER_ID"] == $photo["user_id"]) {
					
					$is_owner = true;
					
				}
				
			} else if(isset($_COOKIE["MY_PHOTOS"])) {
				
				$my_photo_array = json_decode($_COOKIE["MY_PHOTOS"]);
											
				if(in_array($photo["id"], $my_photo_array)) {
					
					$is_owner = true;
					
				}
				
			}
			
			if($is_owner) {
							
				$photos_query = $dbh->prepare("UPDATE photo SET title = :title WHERE id = :photo_id");
				$photos_query->bindParam(":title", $title);
				$photos_query->bindParam(":photo_id", $photo_id);
				$photos_query->execute();	
				
				$result["status"] = 1;
				
			} else {
				
				$result["error"] = "You are not allowed to edit this photo title...";	
				
			}
			
		}  else {
			
			$result["error"] = "Couldn't find this photo...";
	
		}
	
	}
} else {
	
	$result["error"] = "Oops";
	
}

echo json_encode($result);
?>