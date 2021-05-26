<?php
include("../includes/config.php");
include("../includes/db_connect.php");
include("../includes/functions.php");
include("../includes/upload.class.php");

$result = array();

if(isset($_POST["photo_unique_id"]) && isset($_POST["base_64_img"]) && isset($_POST["photo_name"])) {
	
	$user_id = $_SESSION["USER_ID"];
	$photo_unique_id = $_POST["photo_unique_id"];
	$base_64_img = $_POST["base_64_img"];
	$photo_name = htmlspecialchars($_POST["photo_name"]);
	
	// First we get the non-cropped photo in the database
	$photo_query = $dbh->prepare("SELECT id, url, folder_path, filename, ext FROM file WHERE unique_id = :unique_id AND user_id = :user_id");
	$photo_query->bindParam(":unique_id", $photo_unique_id);
	$photo_query->bindParam(":user_id", $user_id);
	$photo_query->execute();
	
	$nb_res_photo = $photo_query->rowCount();	
	
	if($nb_res_photo > 0) {
		
		$uploaded_photo = $photo_query->fetch();
		
		$base_64_img = preg_replace('#^data:image/[^;]+;base64,#', '', $base_64_img);
		
		$handle = new Upload('base64:'.$base_64_img);
		
		if ($handle->uploaded) {
			
			$uploaded_folder_path = $uploaded_photo["folder_path"];
			$uploaded_photo_id = $uploaded_photo["id"];
			$photo_name = $uploaded_photo["filename"];
			$ext = $uploaded_photo["ext"];
		
			$handle->image_resize         	= false;
			$handle->file_overwrite 		= true;
			$handle->file_new_name_body 	= substr($photo_name, 0, strrpos($photo_name, '.'));
			$handle->file_new_name_ext 		= $ext;

			$handle->file_auto_rename		= false;
			
			$complete_dir_name = '../' . $uploaded_folder_path;
								
			$handle->Process($complete_dir_name);
			
			if ($handle->processed) {
				
				$photo_url = $complete_dir_name.$handle->file_dst_name;
				$photo_url_path = str_replace("../", "", $photo_url);
				
				$stmt = $dbh->prepare("UPDATE 
									   file 
									   SET 
									   url = :url,
									   thumb_url = :url,
									   updated_at = NOW()
									   WHERE id = :photo_id");
									
				$stmt->bindParam(':url', $photo_url_path);
				$stmt->bindParam(':photo_id', $uploaded_photo_id);
				$stmt->execute();
				
				$result["success"] = 1;			
				$handle->clean();
				
						
			} else {
				
				$result["error_log"] = $handle->error;
				$result["error"] = "Error while saving the edited version of the photo... Please retry.";
				$result["status"] = 998;
			}
			
		
		}
		
	}
	
} else {
	
	$result["error"] = "Oops";
	
}

echo json_encode($result);