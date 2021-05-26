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

if(isset($_POST["copy_path"]) && isset($_POST["files_ids"]) && isset($_POST["action"])) {

	$files_ids = json_decode($_POST["files_ids"]);
	$copy_path = htmlspecialchars($_POST["copy_path"]);
	$action = htmlspecialchars($_POST["action"]);
	
	
	if(empty($copy_path)) {
		
		$result["error"] = "Please select the album path...";
		 
	} else {
		
		// Get infos about the current user...
		$user_infos = $dbh->prepare("SELECT u.id, u.unique_id, u.stripe_plan, p.diskspace AS max_diskspace, p.bandwidth AS max_bandwidth FROM user u, plan p WHERE u.id = :user_id AND u.plan_id = p.id");
		$user_infos->bindParam(":user_id", $user_id);
		$user_infos->execute();
		
		if($user_infos->rowCount() == 0) {
			
			$result["error"] = "There is an issue with your profile. Please try again later.";
			
		} else if(sizeof($files_ids) == 0) {
			
			$result["error"] = "Please select at least one file to $action...";
			
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
	            	$file_folder_original_path = $file_info["folder_path"];
	                
	            }
				
			}
			
			// If the user tries to copy the files in the same current location
			if($copy_path == $file_folder_original_path) {
				
				$result["error"] = "Please choose an album different from the actual one they are in to copy your files.";
				
			} else {
				
				// OK all check done, we can copy the files
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
	                								   f.bandwidth,
	                								   f.created_at,
	                								   f.is_picture,
	                								   f.filename,
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
		            	$file_current_url = $file_info["url"];
		            	$file_filename = $file_info["filename"];
		            	
		            	// DO THE COPY OF THE FILE
		            	if($action == "copy") {
		            		copy("../" . $file_current_url, "../" . $copy_path . $file_filename);
		            	} else {
			            	rename("../" . $file_current_url, "../" . $copy_path . $file_filename);
		            	}
		            	
		            	$file_url_path = $copy_path . $file_filename;
		            	
		            	// INSERT INTO THE DB
		            	$ip_address = $_SERVER["REMOTE_ADDR"];
						$short_id = gen_uid(8);
						$unique_file_id = sha1(mt_rand(0,99999) . time());
						
						// Get the file size
						$file_weight = filesize("../" . $file_url_path);
						$bandwidth = $file_info["bandwidth"];
						$ext = $file_info["ext"];
						$file_final_name = $file_info["title"];
						$file_name = $file_info["filename"];
						$is_picture = $file_info["is_picture"];
						
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
						$stmt->bindParam(':folder_path', $copy_path);
						$stmt->bindParam(':file_weight', $file_weight);
						$stmt->bindParam(':bandwidth', $bandwidth);
						$stmt->bindParam(':ext', $ext);
						$stmt->bindParam(':ip_address', $ip_address);
						$stmt->bindParam(':user_id', $user_id);
						$stmt->bindParam(':photo_title', $file_final_name);
						$stmt->bindParam(':filename', $file_name);
						$stmt->bindParam(':is_picture', $is_picture);
						$stmt->execute();
												
						// We delete the original file in the db if it was a move...
						if($action == "move") {
							
							$stmt = $dbh->prepare("DELETE FROM file WHERE id = :file_id");
							$stmt->bindParam(":file_id", $file_id);
							$stmt->execute();
							
						}
								            	
		            }
					
				}
				
				$result["success"] = 1;
				
			}
			
		}
		
	}
	
}

echo json_encode($result);