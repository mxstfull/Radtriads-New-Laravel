<?php
include __DIR__ . "/../includes/config.php";
include __DIR__ . "/../includes/db_connect.php";
include __DIR__ . "/../includes/upload.class.php";
include __DIR__ . "/../includes/functions.php";

$result = array();

if(	isset($_POST["user_id"]) ) 
{
		
	$user_id 		= $_POST["user_id"];
	
	if(TEST_MODE) {
		$error = "You can't delete this account in demo mode.";
		$result["has_error"] = true;
		$result["error"] = $error;
		$result["error_txt"] = $error;
	} else {
	
		if($_SESSION["RANK"] < 1) {
	
			$result["status"] = 0;
			$result["error"] = "You're not an admin";
			
		} else {

			$stmt = $dbh->prepare("	DELETE FROM user
									WHERE id = :user_id
									");
			$stmt->bindParam(':user_id', $user_id);
			$stmt->execute();
			
			$photos_query = $dbh->prepare("SELECT * FROM file WHERE user_id = :user_id ORDER BY id");
			$photos_query->bindParam(":user_id", $user_id);
			$photos_query->execute();	
			
			// Delete photo from the hosting
			while($photo = $photos_query->fetch(PDO::FETCH_ASSOC)) {
				
				$photo_url = "../" . $photo["url"];
				
				unlink($photo_url);
				
			}
			
			// Delete all photos of this user
			$stmt = $dbh->prepare("	DELETE FROM file
									WHERE user_id = :user_id
									");
			$stmt->bindParam(':user_id', $user_id);
			$stmt->execute();
			
			// Delete all photos of this user
			$stmt = $dbh->prepare("	DELETE FROM album
									WHERE user_id = :user_id
									");
			$stmt->bindParam(':user_id', $user_id);
			$stmt->execute();
			
			// Delete all photos of this user
			$stmt = $dbh->prepare("	DELETE FROM album_home
									WHERE user_id = :user_id
									");
			$stmt->bindParam(':user_id', $user_id);
			$stmt->execute();
			
			$result["status"] = 1;

		}
	
	}
	
}

echo json_encode($result);
?>