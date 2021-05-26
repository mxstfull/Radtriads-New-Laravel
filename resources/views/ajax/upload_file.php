<?php
include("../includes/config.php");
include("../includes/functions.php");
include("../includes/db_connect.php");
include("../includes/upload.class.php");

require_once("../vendor/autoload.php");

$result = array();

if(!$_SESSION) {
	exit;
}

$user_id = $_SESSION["USER_ID"];
$is_picture = 0;
$thumb_url_path = "";
$file_mime_type = "";

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
		$ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
		$file_body_name = pathinfo($file_name, PATHINFO_FILENAME);
		
		$file_url = "";
		$file_url_path = "";
		$file_final_name = "";
		$bandwidth = 0;
		
		if(!empty($file) && $file["size"] > 0) {
			
			$err_img = false;
	
			// If it's a non-manipulable file
			if($ext != "jpg" && $ext != "jpeg" && $ext != "png" && $ext != "webp") {
								
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
				
				try {
				
					// First we check if the image is safe with the Google API				
					// Init the Google vision API
					$vision = new \Vision\Vision(
					    GOOGLE_API_KEY, 
					    [
					        // See a list of all features in the table below
					        // Feature, Limit
					        new \Vision\Feature(\Vision\Feature::SAFE_SEARCH_DETECTION, 100),
					    ]
					); 
					
					$imagePath = $file['tmp_name'];
					$response = $vision->request(
					    // See a list of all image loaders in the table below
					    new \Vision\Request\Image\LocalImage($imagePath)
					);
					
					$safe_search_results = $response->getSafeSearchAnnotation();
										
					$adult_img = $safe_search_results->getAdult();
					$spoof_img = $safe_search_results->getSpoof();
					$violence_img = $safe_search_results->getViolence();
					$racy_img = $safe_search_results->getRacy();
				
				} catch(Exception $e) {
					$adult_img = "";
					$spoof_img = "";
					$violence_img = "";
					$racy_img = "";
					
				}
				
				if($adult_img == "VERY_LIKELY") {
					
					$result["error"] = "It seems like there is an adult image that is not allowed on our platform...";
					$result["status"] = 998;
					
				} else if($violence_img == "VERY_LIKELY") {
					
					$result["error"] = "It seems like there is a violence related image that is not allowed on our platform...";
					$result["status"] = 998;
					
				} else {
				
					// First we upload the fullsize photo	
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
											
						$bandwidth = $handle->file_src_size;
											
						// Parameters before uploading the photo
						$handle->image_resize         	= false;
						$handle->file_auto_rename		= true;
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
							
							// Create a TMP version for the thumbnail
							$file_url_tmp = $complete_dir_name."tmp_".$file_name;
							copy($file_url, $file_url_tmp);
							
							$file_url_path = str_replace("../", "", $file_url);
							
							$file_url_path = str_replace("//", "/", $file_url_path);
							$result["file_url"] = $file_url_path;
							
						} else {
							$err_img = true;
						}
				
					} else {
						$err_img = true;
					}
					
					
					// If no error for the fullsize photo, we upload the thumbnail
					if(!$err_img) {
											
						$handle_thumb = new Upload($file_url_tmp);
						$thumb_mime_type = $handle_thumb->file_src_mime;
							
						if ($handle_thumb->uploaded) {
														
							$file_location = $complete_dir_name . $file_name;
							$file_final_name = $file_body_name . '.'.$ext;
																										
																							
							// Parameters before uploading the photo
							$handle_thumb->webp_quality 			= 70;
							$handle_thumb->jpeg_quality 			= 70;
							$handle_thumb->png_compression 			= 9;
							$handle_thumb->file_auto_rename			= true;
							$handle_thumb->image_resize 			= true;
							$handle_thumb->image_x 					= 600;
							$handle_thumb->image_ratio_y		 	= true;
							$handle_thumb->file_name_body_pre 		= 'thumb_';
							$handle_thumb->file_new_name_body   	= $file_name_body;
													
							$handle_thumb->Process($complete_dir_name);
													
							if ($handle_thumb->processed) {
															
								$err_img = false;
								$handle_thumb->clean();
								$has_img = true;
								$thumb_file_name = $handle_thumb->file_dst_name;
								$is_picture = 1;
															
								$complete_dir_name_path = str_replace("../", "", $complete_dir_name); 
								$complete_dir_name_path = str_replace("//", "/", $complete_dir_name_path);
								$thumb_url = $complete_dir_name.$thumb_file_name;
								$thumb_url_path = str_replace("../", "", $thumb_url);
								
								$thumb_url_path = str_replace("//", "/", $thumb_url_path);
								$result["thumb_url"] = $thumb_url_path;
															
							} else {
								$err_img = true;
							}
					
						} else {
							$err_img = true;
						}
						
					}
					
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
									   thumb_url = :thumb_url,
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
				$stmt->bindParam(':thumb_url', $thumb_url_path);
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
								
				$dbh->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING );
				
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
				if($ext != "jpg" && $ext != "jpeg" && $ext != "png" && $ext != "webp") {
					
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
							$file_url = $complete_dir_name.$file_name;
							
							// Create a TMP version for the thumbnail
							$file_url_tmp = $complete_dir_name."tmp_".$file_name;
							copy($file_url, $file_url_tmp);
							
							$file_url_path = str_replace("../", "", $file_url);
							$file_url_path = str_replace("//", "/", $file_url_path);
							
							$result["file_url"] = $file_url_path;
							
						} else {
							$err_img = true;
						}
				
					} else {
						$err_img = true;
					}
					
					// If no error for the fullsize photo, we upload the thumbnail
					if(!$err_img) {
											
						$handle_thumb = new Upload($file_url_tmp);
						$thumb_mime_type = $handle_thumb->file_src_mime;
							
						if ($handle_thumb->uploaded) {
														
							$file_location = $complete_dir_name . $file_name;
							$file_final_name = $file_body_name . '.'.$ext;
																							
							// Parameters before uploading the photo
							$handle_thumb->webp_quality 			= 70;
							$handle_thumb->jpeg_quality 			= 70;
							$handle_thumb->png_compression 			= 9;
							$handle_thumb->file_auto_rename			= true;
							$handle_thumb->image_resize 			= true;
							$handle_thumb->image_x 					= 600;
							$handle_thumb->image_ratio_y		 	= true;
							$handle_thumb->file_name_body_pre 		= 'thumb_';
							$handle_thumb->file_new_name_body   	= $file_name_body;
													
							$handle_thumb->Process($complete_dir_name);
													
							if ($handle_thumb->processed) {
															
								$err_img = false;
								$handle_thumb->clean();
								$has_img = true;
								$thumb_file_name = $handle_thumb->file_dst_name;
								$is_picture = 1;
															
								$complete_dir_name_path = str_replace("../", "", $complete_dir_name); 
								$complete_dir_name_path = str_replace("//", "/", $complete_dir_name_path);
								$thumb_url = $complete_dir_name.$thumb_file_name;
								$thumb_url_path = str_replace("../", "", $thumb_url);
								
								$thumb_url_path = str_replace("//", "/", $thumb_url_path);
								$result["thumb_url"] = $thumb_url_path;
															
							} else {
								$err_img = true;
							}
					
						} else {
							$err_img = true;
						}
						
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
					
					$dir_name_path = str_replace("../", "", $complete_dir_name_path);
					
					// Insert into the DB
					$stmt = $dbh->prepare("INSERT INTO 
										   file 
										   SET 
										   filename = :filename,
										   folder_path = :folder_path,
										   unique_id = :unique_id,
										   short_id = :short_id,
										   url = :file_url,
										   thumb_url = :thumb_url,
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
					$stmt->bindParam(':thumb_url', $thumb_url_path);
					$stmt->bindParam(':folder_path', $dir_name_path);
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
		
		<<<<<<<< THIS HAS TO BE COMMENTED
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
		>>>>>>>>>>>>>
		
		$result["nb_uploaded"] = $file_count;
		$result["multiple_upload"] = 1;
		$result["status"] = 1;
		
	}
	*/
				
	if($upload_path != "") {
			
		$result["redirect_dir"] = "uploads/$user_unique_id$upload_path";
		
	}
	
}

echo json_encode($result);
?>