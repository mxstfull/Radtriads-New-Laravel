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

if(isset($_FILES["file"])) {
	
	$file = $_FILES["file"];
	$file_array = reArrayFiles($file);
	
	// Check if we have a particular path defined
	if(isset($_POST["upload_path"])) {
		
		$upload_path = $_POST["upload_path"];
				
	} else {
		
		$upload_path = "";
		
	}
	
		
	$file_count = count($file_array);
	
	// Get the infos about the current user
	$get_user_query = $dbh->prepare("SELECT unique_id FROM user WHERE id = :user_id");
	$get_user_query->bindParam(":user_id", $user_id);
	$get_user_query->execute();
	
	$user = $get_user_query->fetch();
	$user_unique_id = $user["unique_id"];
	
	// If only one photo is uploaded
	if($file_count == 1) {
	
		$file = $file_array[0];
	
		$file_name = $file['name'];
		$ext = pathinfo($file_name, PATHINFO_EXTENSION);
		$file_body_name = pathinfo($file_name, PATHINFO_FILENAME);
		
		$file_url = "";
		$file_url_path = "";
		$file_final_name = "";
		$bandwidth = 0;
		
		if(!empty($file) && $file["size"] > 0) {
			
			$err_img = false;
	
			// If it's a non-manipulable file
			if($ext == "gif" || $ext == "mp4" || $ext == "mov" || $ext == "swf" || $ext == "flv" || $ext == "mp3" || $ext == "wav" || $ext == "txt" || $ext == "rtf" || $ext == "html" || $ext == "php" || $ext == "css" || $ext == "xml" || $ext == "pdf" || $ext == "word" || $ext == "java") {
				
				// Generate a unique directory name
				$today_dir_name = date('Ymd');
				$complete_dir_name = '../uploads/' . $user_unique_id . '/' . $upload_path . '/';
				$complete_dir_name = str_replace("//", "/", $complete_dir_name);
				
				if (!is_dir($complete_dir_name)) {
					mkdir($complete_dir_name);
				}
					
				$file_location = $complete_dir_name . $file_name;
				$file_final_name = $file_body_name . '.'.$ext;
								
				// If file already exists, we rename it!
				if(file_exists($file_location)) {
					
				    $i = 1;
				    
					while(file_exists($file_location))
					{           
					    $file_final_name = $file_body_name.$i.'.'.$ext;
					    $file_location = $complete_dir_name.$file_final_name;
					    $i++;
					}
				    
				}
				
				$file_name = $file_final_name;
								
				$complete_dir_name_path = str_replace("../", "", $complete_dir_name); 
				$file_url_path = str_replace("../", "", $file_location);
				
				move_uploaded_file($file['tmp_name'], $file_location);
				
				$bandwidth = filesize("../" . $file_url_path);
				
				$err_img = false;
				$result["is_file"] = 1;
				
			} else {
						
				$handle = new Upload($file);
				$file_mime_type = $handle->file_src_mime;
					
				if ($handle->uploaded) {
					
					// Generate a unique directory name
					$today_dir_name = date('Ymd');
					$complete_dir_name = '../uploads/' . $user_unique_id . '/' . $upload_path . '/';
					$complete_dir_name = str_replace("//", "/", $complete_dir_name);
					
					if (!is_dir($complete_dir_name)) {
						mkdir($complete_dir_name);
					}
					
					$file_location = $complete_dir_name . $file_name;
					$file_name_body = $file_body_name;
					$file_final_name = $file_body_name . '.'.$ext;
									
					// If file already exists, we rename it!
					if(file_exists($file_location)) {
						
					    $i = 1;
					    
						while(file_exists($file_location))
						{         
							$file_name_body = $file_body_name.$i;  
						    $file_final_name = $file_name_body.'.'.$ext;
						    $file_location = $complete_dir_name.$file_final_name;
						    $i++;
						}
					    
					}
										
					$bandwidth = $handle->file_src_size;
					
					// Parameters before uploading the photo
					$handle->image_resize         	= false;
					$handle->png_compression 		= 8;
					$handle->webp_quality 			= 80;
					$handle->jpeg_quality 			= 80;
					$handle->file_new_name_body   	= $file_name_body;
					
					$handle->Process($complete_dir_name);
					
					if ($handle->processed) {
						
						$err_img = false;
						$handle->clean();
						$has_img = true;
						$file_name = $handle->file_dst_name;
						$is_picture = 1;
						
						$complete_dir_name_path = str_replace("../", "", $complete_dir_name); 
						$complete_dir_name_path = str_replace("//", "/", $complete_dir_name_path);
						$file_url = $complete_dir_name.$file_name;
						$file_url_path = str_replace("../", "", $file_url);
						
						$file_url_path = str_replace("//", "/", $file_url_path);
						$result["file_url"] = $file_url_path;
						
					} else {
						$err_img = true;
					}
			
				} else {
					$err_img = true;
				}
			
			}
			
			if($err_img) {
				
				$result["error"] = "Please upload a JPG or PNG photo only!";
				$result["status"] = 998;
				
			} else {
				
				$ip_address = $_SERVER["REMOTE_ADDR"];
				$short_id = gen_uid(8);
				$unique_file_id = sha1(mt_rand(0,99999) . time());
				
				// Get the file size
				$file_weight = filesize("../" . $file_url_path);
				
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
									   is_picture = :is_picture,
									   status = 1");
									
				$stmt->bindParam(':unique_id', $unique_file_id);
				$stmt->bindParam(':short_id', $short_id);
				$stmt->bindParam(':file_url', $file_url_path);
				$stmt->bindParam(':folder_path', $complete_dir_name_path);
				$stmt->bindParam(':file_weight', $file_weight);
				$stmt->bindParam(':bandwidth', $bandwidth);
				$stmt->bindParam(':ext', $ext);
				$stmt->bindParam(':ip_address', $ip_address);
				$stmt->bindParam(':user_id', $user_id);
				$stmt->bindParam(':photo_title', $file_final_name);
				$stmt->bindParam(':filename', $file_name);
				$stmt->bindParam(':is_picture', $is_picture);
				$stmt->execute();
				
				$file_id = $dbh->lastInsertId();
				
				$result["file_id"] = $file_id;
				$result["file_unique_id"] = $unique_file_id;
				$result["file_mime_type"] = $file_mime_type;
				$result["status"] = 1;	
				
				
			}
			
		} else {
			
			$result["error"] = "Error while uploading... Please retry.";
			$result["status"] = 998;
			
		}
	
	}
	// If multiple files upload
	else {
		
		foreach($file_array as $file) {
	
			$file_name = $file['name'];
			$ext = pathinfo($file_name, PATHINFO_EXTENSION);
			$file_body_name = pathinfo($file_name, PATHINFO_FILENAME);
			
			$file_url = "";
			$file_url_path = "";
			$file_final_name = "";
			$bandwidth = 0;
			$is_picture = 0;
			
			if(!empty($file) && $file["size"] > 0) {
				
				$err_img = false;
		
				// If it's a non-manipulable file
				if($ext == "gif" || $ext == "mp4" || $ext == "mov" || $ext == "swf" || $ext == "flv" || $ext == "mp3" || $ext == "wav" || $ext == "txt" || $ext == "rtf" || $ext == "html" || $ext == "php" || $ext == "css" || $ext == "xml" || $ext == "pdf" || $ext == "word" || $ext == "java") {
					
					// Generate a unique directory name
					$today_dir_name = date('Ymd');
					$complete_dir_name = '../uploads/' . $user_unique_id . '/' . $upload_path . '/';
					$complete_dir_name = str_replace("//", "/", $complete_dir_name);
					
					if (!is_dir($complete_dir_name)) {
						mkdir($complete_dir_name);
					}
						
					$file_location = $complete_dir_name . $file_name;
					$file_final_name = $file_body_name . '.'.$ext;
									
					// If file already exists, we rename it!
					if(file_exists($file_location)) {
						
					    $i = 1;
					    
						while(file_exists($file_location))
						{           
						    $file_final_name = $file_body_name.$i.'.'.$ext;
						    $file_location = $complete_dir_name.$file_final_name;
						    $i++;
						}
					    
					}
					
					$file_name = $file_final_name;
									
					$complete_dir_name_path = str_replace("../", "", $complete_dir_name); 
					$complete_dir_name_path = str_replace("//", "/", $complete_dir_name_path);
					$file_url_path = str_replace("../", "", $file_location);
					
					move_uploaded_file($file['tmp_name'], $file_location);
					
					$bandwidth = filesize("../" . $file_url_path);
					
					$err_img = false;
					$result["is_file"] = 1;
					
				} else {
							
					$handle = new Upload($file);
					$file_mime_type = $handle->file_src_mime;
						
					if ($handle->uploaded) {
						
						// Generate a unique directory name
						$today_dir_name = date('Ymd');
						$complete_dir_name = '../uploads/' . $user_unique_id . '/' . $upload_path . '/';
						$complete_dir_name = str_replace("//", "/", $complete_dir_name);
						
						if (!is_dir($complete_dir_name)) {
							mkdir($complete_dir_name);
						}
						
						$file_location = $complete_dir_name . $file_name;
						$file_name_body = $file_body_name;
						$file_final_name = $file_body_name . '.'.$ext;
										
						// If file already exists, we rename it!
						if(file_exists($file_location)) {
							
						    $i = 1;
						    
							while(file_exists($file_location))
							{         
								$file_name_body = $file_body_name.$i;  
							    $file_final_name = $file_name_body.'.'.$ext;
							    $file_location = $complete_dir_name.$file_final_name;
							    $i++;
							}
						    
						}
											
						$bandwidth = $handle->file_src_size;
						
						// Parameters before uploading the photo
						$handle->image_resize         	= false;
						$handle->png_compression 		= 8;
						$handle->webp_quality 			= 80;
						$handle->jpeg_quality 			= 80;
						$handle->file_new_name_body   	= $file_name_body;
						
						$handle->Process($complete_dir_name);
						
						if ($handle->processed) {
							
							$err_img = false;
							$handle->clean();
							$has_img = true;
							$file_name = $handle->file_dst_name;
							$is_picture = 1;
							
							$complete_dir_name_path = str_replace("../", "", $complete_dir_name); 
							$complete_dir_name_path = str_replace("//", "/", $complete_dir_name); 
							$file_url = $complete_dir_name.$file_name;
							$file_url_path = str_replace("../", "", $file_url);
							$file_url_path = str_replace("//", "/", $file_url_path);
							
							$result["file_url"] = $file_url_path;
							
						} else {
							$err_img = true;
						}
				
					} else {
						$err_img = true;
					}
				
				}
				
				if($err_img) {
					
					$result["error"] = "Please upload a JPG or PNG photo only!";
					$result["status"] = 998;
					
				} else {
					
					$ip_address = $_SERVER["REMOTE_ADDR"];
					$short_id = gen_uid(8);
					$unique_file_id = sha1(mt_rand(0,99999) . time());
					
					// Get the file size
					$file_weight = filesize("../" . $file_url_path);
					
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
										   is_picture = :is_picture,
										   status = 1");
										
					$stmt->bindParam(':unique_id', $unique_file_id);
					$stmt->bindParam(':short_id', $short_id);
					$stmt->bindParam(':file_url', $file_url_path);
					$stmt->bindParam(':folder_path', $complete_dir_name_path);
					$stmt->bindParam(':file_weight', $file_weight);
					$stmt->bindParam(':bandwidth', $bandwidth);
					$stmt->bindParam(':ext', $ext);
					$stmt->bindParam(':ip_address', $ip_address);
					$stmt->bindParam(':user_id', $user_id);
					$stmt->bindParam(':photo_title', $file_final_name);
					$stmt->bindParam(':filename', $file_name);
					$stmt->bindParam(':is_picture', $is_picture);
					$stmt->execute();
					
					$file_id = $dbh->lastInsertId();
					
					$result["file_id"] = $file_id;
					$result["file_unique_id"] = $unique_file_id;
					$result["file_mime_type"] = $file_mime_type;
					$result["status"] = 1;	
					
					
				}
				
			} else {
				
				$result["error"] = "Error while uploading... Please retry.";
				$result["status"] = 998;
				
			}
		}
		
		/*
		foreach ($file_array as $file) {
			
			$path = $file['name'];
			$ext = pathinfo($path, PATHINFO_EXTENSION);
			
			$photo_url = "";
			$photo_url_path = "";
			
			if(!empty($photo) && $photo["size"] > 0) {
				
				$err_img = false;
						
				// Generate a unique name for the directory
				$unique_photo_name = sha1(time() . mt_rand(0,99999));
		
				// If it's a GIF
				if($ext == "gif") {
					
					$gif_name = $unique_photo_name . ".gif";
					$photo_url = "../uploads/" . $gif_name;
					$photo_url_path = str_replace("../", "", $photo_url);
					
					move_uploaded_file($photo['tmp_name'], $photo_url);
					
					$err_img = false;
					$result["is_gif"] = 1;
					
				} else {
							
					$handle = new Upload($photo);
					$photo_mime_type = $handle->file_src_mime;
						
					if ($handle->uploaded) {
						
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
				
				}
				
				if($err_img) {
					
					$result["error"] = "Please upload a JPG or PNG photo only!";
					$result["status"] = 998;
					break;
					
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
					
					$photo_title = "File #$photo_id";
					
					$stmt = $dbh->prepare("UPDATE 
										   photo 
										   SET 
										   title = :photo_title
										   WHERE id = :photo_id");
										
					$stmt->bindParam(':photo_title', $photo_title);
					$stmt->bindParam(':photo_id', $photo_id);
					$stmt->execute();
					
					
				}
				
			}
		
		}
		*/
		
		$result["nb_uploaded"] = $file_count;
		$result["multiple_upload"] = 1;
		$result["status"] = 1;
		
	}
				
	if($upload_path != "") {
			
		$result["redirect_dir"] = "uploads/$user_unique_id$upload_path";
		
	}
	
}

echo json_encode($result);
?>