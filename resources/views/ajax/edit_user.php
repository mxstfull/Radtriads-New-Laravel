<?php
include("../includes/config.php");
include("../includes/db_connect.php");
include("../includes/upload.class.php");
include("../includes/functions.php");

$result = array();

if(	isset($_POST["user_id"]) ) 
{
		
	$user_id 		= $_POST["user_id"];
	
	if(TEST_MODE) {
		$error = "You can't edit this account in demo mode.";
		$result["has_error"] = true;
		$result["error"] = $error;
		$result["error_txt"] = $error;
	} else {
	
		if($_SESSION["RANK"] < 1) {
	
			$result["status"] = 0;
			$result["error"] = "You're not an admin";
			
		} else if($user_id == $_SESSION["USER_ID"]) {
			
			$result["status"] = 0;
			$result["error"] = "You can't edit your own account from there.";
			
		} else {
			
			$new_username = $_POST["username"];
			$new_email = $_POST["email"];
			$new_rank = $_POST["rank"];

			$stmt = $dbh->prepare("	UPDATE user
									SET 
									username = :username,
									email = :email,
									rank = :rank
									WHERE id = :user_id
									");
			
			$stmt->bindParam(':username', $new_username);
			$stmt->bindParam(':email', $new_email);
			$stmt->bindParam(':rank', $new_rank);
			$stmt->bindParam(':user_id', $user_id);
			$stmt->execute();
			
			
			
			$result["status"] = 1;

		}
	
	}
	
}

echo json_encode($result);
?>