<?php
include("../includes/config.php");
include("../includes/db_connect.php");
include("../includes/upload.class.php");

$result = array();

if(isset($_FILES["webcam_img"])) {
	
	// Upload the cropped image
	$photo = $_FILES["webcam_img"];
					
	$handle = new Upload($photo);
	$err_img = false;

	if ($handle->uploaded) {
		
		// Generate a unique name for the directory
		$unique_photo_name = sha1(time() . mt_rand(0,99999));
		
		// Generate a unique directory name
		$today_dir_name = date('Ymd');
		$complete_dir_name = '../uploads/' . $today_dir_name . '/';
		
		if (!is_dir($complete_dir_name)) {
			mkdir($complete_dir_name);
		}
		
		// Parameters before uploading the photo
		$handle->image_resize         	= false;
		$handle->png_compression 		= 8;
		$handle->webp_quality 			= 80;
		$handle->jpeg_quality 			= 80;
		$handle->file_new_name_body   	= $unique_photo_name;
		
		$handle->Process($complete_dir_name);
		
		if ($handle->processed) {
			
			$err_img = false;
			$handle->clean();
			$has_img = true;
			
			$photo_url = $complete_dir_name.$handle->file_dst_name;
			$photo_url_path = str_replace("../", "", $photo_url);
			
			$result["photo_url"] = $photo_url_path;
			
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
		
		if($_SESSION) {
				
			$user_id = $_SESSION["USER_ID"];
			
		} else {
			
			$user_id = NULL;
			
		}
		
		$ip_address = $_SERVER["REMOTE_ADDR"];
		$short_id = gen_uid(8);
		
		// Insert into the DB
		$stmt = $dbh->prepare("INSERT INTO 
							   photo 
							   SET 
							   unique_id = :unique_id,
							   short_id = :short_id,
							   url = :photo_url,
							   created_at = NOW(),
							   updated_at = NOW(),
							   ip_address = :ip_address,
							   user_id = :user_id,
							   status = 1");
							
		$stmt->bindParam(':unique_id', $unique_photo_name);
		$stmt->bindParam(':short_id', $short_id);
		$stmt->bindParam(':photo_url', $photo_url_path);
		$stmt->bindParam(':ip_address', $ip_address);
		$stmt->bindParam(':user_id', $user_id);
		$stmt->execute();
		
		$photo_id = $dbh->lastInsertId();
		
		$photo_title = "Photo #$photo_id";
		
		$stmt = $dbh->prepare("UPDATE 
							   photo 
							   SET 
							   title = :photo_title
							   WHERE id = :photo_id");
							
		$stmt->bindParam(':photo_title', $photo_title);
		$stmt->bindParam(':photo_id', $photo_id);
		$stmt->execute();
		
		// -- Create a cookie so we know this user created the photo
		// If the cookie doesn't exist, we create it
		if(!isset($_COOKIE["MY_PHOTOS"])) {
			
			$photo_array = array();
			array_push($photo_array, $photo_id);
			
			setcookie("MY_PHOTOS", json_encode($photo_array), time()+(3600*24*10), '/');
			
		}
		// If the cookie already exists, we update it
		else {
			
			$photo_array = json_decode($_COOKIE["MY_PHOTOS"]);
			array_push($photo_array, $photo_id);
			
			setcookie("MY_PHOTOS", json_encode($photo_array), time()+(3600*24*10), '/');
							
		}
		
		$result["photo_id"] = $photo_id;
		$result["photo_unique_id"] = $unique_photo_name;
		$result["photo_mime_type"] = $photo_mime_type;
		$result["status"] = 1;
		
		
	}
	
	
} else {
	
	$result["error"] = "Oops";
	
}

echo json_encode($result);
?>