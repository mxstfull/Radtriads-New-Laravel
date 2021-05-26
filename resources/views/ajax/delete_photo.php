<?php
include __DIR__ . "/../includes/config.php";
include __DIR__ . "/../includes/db_connect.php";
include __DIR__ . "/../includes/upload.class.php";
use Illuminate\Support\Facades\File;

$result = array();

if(isset($_POST["photo_id"])) {
	
	$is_owner = false;

	$photo_id = $_POST["photo_id"];
	
		
	// Get all the photos of this user
	$photos_query = $dbh->prepare("SELECT id, user_id, url, thumb_url, is_picture FROM file WHERE id = :photo_id");
	$photos_query->bindParam(":photo_id", $photo_id);
	$photos_query->execute();	
	
	if(TEST_MODE) {
		$result["error"] = "You can't delete the photo in demo mode.";
	} else {
			
		if($photos_query->rowCount() > 0) {
				
			$photo = $photos_query->fetch();
						
			if($_SESSION["RANK"] > 0) {
		
				$is_owner = true;
				
			} else if($_SESSION) {
		
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
				
				$photos_query = $dbh->prepare("DELETE FROM file WHERE id = :photo_id");
				$photos_query->bindParam(":photo_id", $photo_id);
				$photos_query->execute();
				
				$photo_url = $photo["url"];
				File::delete(storage_path('app/').$photo_url);
				if($photo["is_picture"])
					File::delete(storage_path('app/').$photo["thumb_url"]);
				$result["status"] = 1;
				
			} else {
				
				$result["error"] = "You are not allowed to delete this photo...";
		
			}
		
		}
	
	}
	
	
} else {
	
	$result["error"] = "Oops";
	
}

echo json_encode($result);
?>