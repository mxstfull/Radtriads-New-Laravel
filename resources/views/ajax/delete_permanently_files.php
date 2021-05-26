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

if(isset($_POST["files_ids"])) {

	$files_ids = json_decode($_POST["files_ids"]);

	// Get infos about the current user...
	$user_infos = $dbh->prepare("SELECT u.id, u.unique_id, u.stripe_plan, p.diskspace AS max_diskspace, p.bandwidth AS max_bandwidth FROM user u, plan p WHERE u.id = :user_id AND u.plan_id = p.id");
	$user_infos->bindParam(":user_id", $user_id);
	$user_infos->execute();
	
	if($user_infos->rowCount() == 0) {
		
		$result["error"] = "There is an issue with your profile. Please try again later.";
		
	} else if(sizeof($files_ids) == 0) {
		
		$result["error"] = "Please select at least one file to delete...";
		
	} else {
		
		$file_folder_original_path = "";
					
		foreach($files_ids as $file_id) {
							
			$get_file_infos = $dbh->prepare("  SELECT 
            								   f.id AS file_id, 
            								   f.short_id, 
            								   f.title, 
            								   f.unique_id,
            								   f.url,
            								   f.folder_path,
            								   f.ext,
            								   f.diskspace,
            								   f.created_at,
            								   f.is_picture,
            								   f.status
            								   FROM file f
            								   WHERE 
            								   f.user_id = :user_id
            								   AND 
            								   f.id = :file_id
            									");
            
            $get_file_infos->bindParam(":user_id", $user_id);
            $get_file_infos->bindParam(":file_id", $file_id);
            $get_file_infos->execute();
            
            if($get_file_infos->rowCount() > 0) {
             
            	$file_info = $get_file_infos->fetch();
            	$file_id = $file_info["file_id"];
            	
            	$file_folder_original_path = $file_info["folder_path"];
            	
            	unlink("../" . $file_folder_original_path);
            	
            	$delete_file_query = $dbh->prepare("DELETE FROM file WHERE id = :file_id");
				$delete_file_query->bindParam(":file_id", $file_id);
				$delete_file_query->execute();
				
            }
			
		}
		
		$result["success"] = 1;
		
	}
	
}

echo json_encode($result);