<?php
include("../includes/config.php");
include("../includes/db_connect.php");
include("../includes/functions.php");
include("../includes/upload.class.php");

$result = array();

if(isset($_FILES["cropped_img"]) && isset($_POST["uploaded_photo_unique_id"])) {
	
	$user_id = $_SESSION["USER_ID"];
	$uploaded_photo_id = $_POST["uploaded_photo_unique_id"];
	
	if(isset($_POST["save_as_new"])) {
		
		$save_as_new = $_POST["save_as_new"];
		
	} else {
		
		$save_as_new = 0;
		
	}
	
	// First we get the non-cropped photo in the database
	$photo_query = $dbh->prepare("SELECT id, url, folder_path, filename, ext FROM file WHERE unique_id = :uploaded_photo_id AND user_id = :user_id");
	$photo_query->bindParam(":uploaded_photo_id", $uploaded_photo_id);
	$photo_query->bindParam(":user_id", $user_id);
	$photo_query->execute();
	
	$nb_res_photo = $photo_query->rowCount();	
	
	if($nb_res_photo > 0) {
		
		$uploaded_photo = $photo_query->fetch();
		
		$uploaded_photo_id = $uploaded_photo["id"];
		$uploaded_folder_path = $uploaded_photo["folder_path"];
		$uploaded_filename = $uploaded_photo["filename"];
		$path_parts = pathinfo($uploaded_photo["url"]);
		$ext = $uploaded_photo["ext"];
	
		// Upload the cropped image
		$photo = $_FILES["cropped_img"];
						
		$handle = new Upload($photo);
		$err_img = false;
	
		if ($handle->uploaded) {
			
			// Generate a unique name for the directory
			$unique_photo_name = sha1(time() . mt_rand(0,99999));
			
			// Generate a unique directory name
			$today_dir_name = date('Ymd');
			$complete_dir_name = '../' . $uploaded_folder_path;
			
			if (!is_dir($complete_dir_name)) {
				mkdir($complete_dir_name);
			}
			
			// Parameters before uploading the photo
			$handle->image_resize         	= false;
			$handle->png_compression 		= 8;
			$handle->webp_quality 			= 80;
			$handle->jpeg_quality 			= 80;
			$handle->file_new_name_body   	= $path_parts['filename'];
			
			$handle->Process($complete_dir_name);
			
			if ($handle->processed) {
				
				$err_img = false;
				$handle->clean();
				$has_img = true;
				
				$photo_url = $complete_dir_name.$handle->file_dst_name;
				$photo_url_path = str_replace("../", "", $photo_url);
				$new_file_name = $handle->file_dst_name;
				
				$result["file_url"] = $photo_url_path;
				
			} else {
				$err_img = true;
			}
	
		} else {
			$err_img = true;
		}
		
		if($err_img) {
			
			$result["error"] = "Please upload a JPG or PNG photo only!";
			$result["status"] = 998;
			
		} else {
			
			if($save_as_new == 0) {
	
				// Remove the old non-cropped version from the filesystem
				$old_photo_path = "../".$uploaded_photo["url"];
				unlink($old_photo_path);
				
				$stmt = $dbh->prepare("UPDATE 
									   file 
									   SET 
									   url = :url,
									   filename = :filename,
									   updated_at = NOW()
									   WHERE id = :photo_id");
									
				$stmt->bindParam(':url', $photo_url_path);
				$stmt->bindParam(':filename', $new_file_name);
				$stmt->bindParam(':photo_id', $uploaded_photo_id);
				$stmt->execute();
			
			} else {
				
				$unique_file_id = sha1(mt_rand(0,99999) . time());
				$ip_address = $_SERVER["REMOTE_ADDR"];
				$short_id = gen_uid(8);
				
				$file_weight = filesize("../" . $photo_url_path);
				$bandwidth = filesize("../" . $photo_url_path);
				$file_final_name = $uploaded_filename;
								
				// Insert into the DB
				$stmt = $dbh->prepare("INSERT INTO 
									   file 
									   SET 
									   filename = :filename,
									   folder_path = :folder_path,
									   unique_id = :unique_id,
									   short_id = :short_id,
									   url = :file_url,
									   diskspace = :file_weight,
									   bandwidth = :bandwidth,
									   ext = :ext,
									   created_at = NOW(),
									   updated_at = NOW(),
									   ip_address = :ip_address,
									   user_id = :user_id,
									   title = :photo_title,
									   is_picture = 1,
									   status = 1");
									
				$stmt->bindParam(':unique_id', $unique_file_id);
				$stmt->bindParam(':short_id', $short_id);
				$stmt->bindParam(':file_url', $photo_url_path);
				$stmt->bindParam(':folder_path', $uploaded_folder_path);
				$stmt->bindParam(':file_weight', $file_weight);
				$stmt->bindParam(':bandwidth', $bandwidth);
				$stmt->bindParam(':ext', $ext);
				$stmt->bindParam(':ip_address', $ip_address);
				$stmt->bindParam(':user_id', $user_id);
				$stmt->bindParam(':photo_title', $file_final_name);
				$stmt->bindParam(':filename', $new_file_name);
				$stmt->execute();
				
				$file_id = $dbh->lastInsertId();
				
				$result["file_id"] = $unique_file_id;
				
			}
			
			
			$result["status"] = 1;	
			
			
		}
	
	}
	
} else {
	
	$result["error"] = "Oops";
	
}

echo json_encode($result);
?>