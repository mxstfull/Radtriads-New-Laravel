<?php
include("../includes/config.php");
include("../includes/db_connect.php");
include("../includes/upload.class.php");

$result = array();

if(!$_SESSION) {
	exit;
}

if(isset($_POST["privacy_setting"]) && isset($_POST["privacy_password"]) && isset($_POST["album_id"]) && isset($_POST["is_dashboard"])) {
	
	$user_id = $_SESSION["USER_ID"];
	$album_id = $_POST["album_id"];
	$privacy_setting = $_POST["privacy_setting"];
	$privacy_password = $_POST["privacy_password"];
	$is_dashboard = $_POST["is_dashboard"];
	
	if(TEST_MODE) {
		$result["error"] = "You can't update the title in demo mode.";
	} else {
			
		$has_error = false;
		
		if($privacy_setting == 2) {
					
			if(strlen($privacy_password) < 3) {
				
				$result["error"] = "Please use a password of at least 3 characters for your album...";
				$has_error = true;
				
			}
			
		}
		
		if(!$has_error) {
		
			// We are updating an album
			if($is_dashboard == 0) {
		
				$get_album_query = $dbh->prepare("SELECT id, title, is_protected FROM album WHERE user_id = :user_id AND id = :album_id");
				$get_album_query->bindParam(":user_id", $user_id);
				$get_album_query->bindParam(":album_id", $album_id);
				$get_album_query->execute();
				
				if($get_album_query->rowCount() == 0) {
					
					$result["error"] = "Oops, seems like you don't own this album...";
					
				} else {
					
					$upd_album_query = $dbh->prepare("  UPDATE album 
														SET 
														is_protected = :is_protected, 
														password = :password
														WHERE id = :album_id");
														
					$upd_album_query->bindParam(":is_protected", $privacy_setting);
					$upd_album_query->bindParam(":password", $privacy_password);
					$upd_album_query->bindParam(":album_id", $album_id);
					$upd_album_query->execute();
					
					$result["success"] = 1;
					
				}
			
			} 
			// We are updating the home album
			else {
				
				$upd_album_query = $dbh->prepare("  UPDATE album_home 
													SET 
													is_protected = :is_protected, 
													password = :password
													WHERE user_id = :user_id");
													
				$upd_album_query->bindParam(":is_protected", $privacy_setting);
				$upd_album_query->bindParam(":password", $privacy_password);
				$upd_album_query->bindParam(":user_id", $user_id);
				$upd_album_query->execute();
				
				$result["success"] = 1;
				
			}
		
		}
	
	}
} else {
	
	$result["error"] = "Oops";
	
}

echo json_encode($result);
?>