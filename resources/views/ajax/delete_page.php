<?php
include __DIR__ . "/../includes/config.php";
include __DIR__ . "/../includes/db_connect.php";
include __DIR__ . "/../includes/upload.class.php";
include __DIR__ . "/../includes/functions.php";

$result = array();

if(	isset($_POST["page_id"]) ) 
{
		
	$page_id 		= $_POST["page_id"];
	
	if(TEST_MODE) {
		$error = "You can't delete this page in demo mode.";
		$result["has_error"] = true;
		$result["error"] = $error;
		$result["error_txt"] = $error;
	} else {
	
		if($_SESSION["RANK"] < 1) {
	
			$result["status"] = 0;
			$result["error"] = "You're not an admin";
			
		} else {
			
			$stmt = $dbh->prepare("	DELETE FROM custom_page
									WHERE id = :page_id
									");
			$stmt->bindParam(':page_id', $page_id);
			$stmt->execute();
			
			$result["status"] = 1;
			
		}
	
	}

		
} else {
	
	$result["error"] = 998;
	$result["has_error"] = true;
	$result["error_txt"] = "This user doesn't exist anymore.";
	
}

echo json_encode($result);
?>