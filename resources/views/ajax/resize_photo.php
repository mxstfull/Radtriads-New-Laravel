<?php
include("../includes/config.php");
include("../includes/db_connect.php");
include("../includes/functions.php");
include("../includes/upload.class.php");
include("../includes/resize.class.php");

$result = array();

if(isset($_POST["new_width"]) && isset($_POST["new_height"]) && isset($_POST["photo_short_id"])) {
	
	$user_id = $_SESSION["USER_ID"];
	$photo_short_id = $_POST["photo_short_id"];
	$new_height = intval($_POST["new_height"]);
	$new_width = intval($_POST["new_width"]);
	
	// First we get the non-cropped photo in the database
	$photo_query = $dbh->prepare("SELECT id, url, folder_path, filename, ext FROM file WHERE short_id = :photo_short_id AND user_id = :user_id");
	$photo_query->bindParam(":photo_short_id", $photo_short_id);
	$photo_query->bindParam(":user_id", $user_id);
	$photo_query->execute();
	
	$nb_res_photo = $photo_query->rowCount();	
	
	if($nb_res_photo > 0) {
		
		$uploaded_photo = $photo_query->fetch();
		
		if($new_height < 50 || $new_width < 50) {
			
			$result["error"] = "Your photo size should not be less than 50px in width or height...";
			
		} else if($new_height > 5000 || $new_width > 5000) {
			
			$result["error"] = "Your photo size should not be more than 5000px in width or height...";
			
		} else {
			
			// Upload the cropped image
			$photo = "../".$uploaded_photo["url"];
			
			$uploaded_photo_id = $uploaded_photo["id"];
			$uploaded_folder_path = $uploaded_photo["folder_path"];
			$uploaded_filename = $uploaded_photo["filename"];
			$path_parts = pathinfo($uploaded_photo["url"]);
			$ext = $uploaded_photo["ext"];
			
			$new_path = "../" . $uploaded_folder_path . $uploaded_filename;
							
			$resize = new ResizeImage($photo);
			$resize->resizeTo($new_width, $new_height, 'exact');
			$resize->saveImage($new_path);
			
			$result["new_path"] = $new_path;
			$result["status"] = 1;	
			
			/*
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
			*/
			
		}
	
	}
	
} else {
	
	$result["error"] = "Oops";
	
}

echo json_encode($result);
?>