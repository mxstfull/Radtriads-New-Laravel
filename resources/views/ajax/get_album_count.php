<?php
include("../includes/config.php");
include("../includes/db_connect.php");
include("../includes/upload.class.php");
include("../includes/functions.php");

$result = array();

if(	isset($_POST["data_path"]) ) 
{
		
	$data_path 		= $_POST["data_path"];
	$user_id		= $_SESSION["USER_ID"];
	
	if(TEST_MODE) {
		$error = "You can't delete this page in demo mode.";
		$result["has_error"] = true;
		$result["error"] = $error;
		$result["error_txt"] = $error;
	} else {
	
		// Get the number of files in this directory
        $nb_files_query = $dbh->prepare("SELECT id FROM file WHERE folder_path = :path AND is_deleted = 0 AND user_id = :user_id");
        $nb_files_query->bindParam(":path", $data_path);
        $nb_files_query->bindParam(":user_id", $user_id);
        $nb_files_query->execute();
        
        $nb_files_path = $nb_files_query->rowCount();
        
        $result["album_count"] = $nb_files_path;
	
	}

		
} else {
	
	$result["error"] = 998;
	$result["has_error"] = true;
	$result["error_txt"] = "This user doesn't exist anymore.";
	
}

echo json_encode($result);
?>