<?php
include("../includes/config.php");
include("../includes/db_connect.php");
include("../includes/upload.class.php");

$result = array();
$nb_files = 0;

if(isset($_POST["last_file_id"]) && isset($_POST["last_file_created_at"]) && isset($_POST["folder_path"])) {
	
	if(!$_SESSION) {
		
		echo "";
		
	} else {
		
		
		$user_id = $_SESSION["USER_ID"];
		
		$get_user_query = $dbh->prepare("SELECT stripe_plan, unique_id, stripe_subscription_id, stripe_customer_id, show_social_share, show_direct_link, show_forum_code, show_html_code FROM user WHERE id = :user_id");
		$get_user_query->bindParam(":user_id", $user_id);
		$get_user_query->execute();
		
		$user = $get_user_query->fetch();
				
		$user_unique_id = $user["unique_id"];
		$plan_selected = $user["stripe_plan"];
		
		$displayed_plan_name = "";
		
		if($plan_selected == "price_1H2C6hI8XlJR7K1Gllvot61P" || $plan_selected == "price_1H2C6hI8XlJR7K1Gj2cxxmmY") {
			$displayed_plan_name = "Silver";
		} 
	
		$show_html_code = $user["show_html_code"];
		$show_direct_link = $user["show_direct_link"];
		$show_forum_code = $user["show_forum_code"];
		$show_social_share = $user["show_social_share"];
		

		$last_file_id = $_POST["last_file_id"];
		$last_file_created_at = $_POST["last_file_created_at"];
		$param_path = $_POST["folder_path"];
		

		$get_file_infos = $dbh->prepare("  SELECT 
        								   f.id AS file_id, 
        								   f.short_id, 
        								   f.updated_at,
        								   f.title, 
        								   f.unique_id,
        								   f.url,
        								   f.thumb_url,
        								   f.ext,
        								   f.diskspace,
        								   f.created_at,
        								   f.is_picture,
        								   f.status
        								   FROM file f
        								   WHERE 
        								   f.user_id = :user_id
        								   AND 
        								   f.folder_path = :folder_path
        								   AND 
        								   f.is_deleted = 0
        								   AND
        								   f.created_at < :last_created_at
        								   ORDER BY
        								   created_at DESC LIMIT 48
        									");
        
        $get_file_infos->bindParam(":user_id", $user_id);
        $get_file_infos->bindParam(":last_created_at", $last_file_created_at);
        $get_file_infos->bindParam(":folder_path", $param_path);
        $get_file_infos->execute();
        
		if($get_file_infos->rowCount() > 0) {
		    
		    while($file_infos = $get_file_infos->fetch(PDO::FETCH_ASSOC)) {
		    
		        $nb_files++;
		        
		        $file_uploaded_date = date("d/m/Y H:i", strtotime($file_infos["created_at"]));
		        $file_type = strtoupper($file_infos["ext"]);
		        $file_size = $file_infos["diskspace"] / 1000;
		        $is_picture = $file_infos["is_picture"];
		        $file_url = $file_infos["url"];
				$thumb_url = $file_infos["thumb_url"];
		        $file_id = $file_infos["file_id"];
		        $file_unique_id = $file_infos["unique_id"];
		        $file_timestamp = strtotime($file_infos["created_at"]);
		        $filename = $file_infos["title"];
		        
		        
			?>
			
			<div class="col-md-3 file_col_container" data-id="<?php echo $file_id; ?>" data-name="<?php echo $filename; ?>" data-timestamp="<?php echo $file_timestamp; ?>" data-created-at="<?php echo $file_infos["created_at"]; ?>">
				<div class="file_container card" data-id="<?php echo $file_id; ?>" data-url="file.php?id=<?php echo $file_unique_id; ?>">
					<div class="card-body">
						<div class="file_actions">
							<div class="file_action_check">
								<i class="far fa-square"></i>
							</div>
						</div>
						<?php
						if($is_picture == 1 || $file_type == "GIF") {
						?>
						<div class="crop_btn_container">
							<a href="download.php?id=<?php echo $file_unique_id; ?>" class="btn-download btn btn-primary btn-sm"><i class="fas fa-download"></i></a>
							<!--<a href="photo-crop.php?id=<?php echo $file_unique_id; ?>" class="btn-crop btn btn-danger btn-sm"><i class="fas fa-crop"></i></a>-->
						</div>
						<div class="is_picture_container f_container">
							<a href="file.php?id=<?php echo $file_unique_id; ?>">
								<?php
								if($thumb_url != "") {
									$p_url = STACKPATH_URL . "/" . $thumb_url;
								} else {
									$p_url = STACKPATH_URL . "/" . $file_url;
								}
								?> 
					
								<img class="lazy" data-src="<?php echo $p_url; ?>?v=<?php echo strtotime($file_infos["updated_at"]); ?>" />
							</a>
						</div>
						<?php
						} else { 
						?>
						<div class="crop_btn_container">
							<a href="download.php?id=<?php echo $file_unique_id; ?>" class="btn-download btn btn-primary btn-sm"><i class="fas fa-download"></i></a>
						</div>
						<div class="is_file_container f_container d-flex align-items-center">
							<a href="file.php?id=<?php echo $file_unique_id; ?>"><?php echo $file_type; ?></a>
							<?php
							if($file_type == "MOV" || $file_type == "FLV" || $file_type == "MP4" || $file_type == "WEBM" || $file_type == "SWF" || $file_type == "OGG") {
								
								if($file_type == "MOV") {
									$file_type_player = "video/mp4";
								} else if($file_type == "FLV" || $file_type == "SWF") {
									$file_type_player = "video/x-flv";
								} else {
									$file_type_player = "video/$file_type";
								}
							?>
							<div class="video_play_btn">
								
								
								<a href="#myVideo" class="btn btn-primary btn-video-play"  data-type="<?php echo strtolower($file_type_player); ?>" data-url="<?php echo $file_url; ?>"><i class="fas fa-play"></i> Play Video</a>
							</div>
							<?php
							}
							?>
						</div>
						<?php
						}	
						?>
						<div class="file_filename">
							<?php echo $filename; ?>
						</div>
						<div class="file_infos">
							Uploaded : <?php echo $file_uploaded_date; ?>
							<br />
							<?php echo $file_type; ?> | <?php echo $file_size; ?>kb
						</div>
		
						<?php
						if($show_direct_link != 0 || $show_forum_code != 0 || $show_html_code != 0 || $show_social_share != 0) {	
						?>
						<div class="file_url_lst">
							<?php
							if($show_direct_link == 1 && $displayed_plan_name != "Silver") {	
							?>
							<div class="form-group">
								<label>Direct URL</label>
								<div class="input-group input-group-copy-link-small">
									<input type="text"  class="form-control" id="direct_link_<?php echo $file_unique_id; ?>" value="<?php echo STACKPATH_URL ."/". $file_url; ?>" />
									<div class="input-group-append">
										<button class="btn btn-primary btn-copy" data-clipboard-target="#direct_link_<?php echo $file_unique_id; ?>" type="button" id="button-addon2">Copy</button>
									</div>
								</div>
							</div>
							<?php
							}
							?>
							
							<?php
							if($show_html_code == 1) {	
							?>
							<div class="form-group">
								<label>HTML Link</label>
								<div class="input-group input-group-copy-link-small">
									<input type="text" class="form-control" id="html_link_<?php echo $file_unique_id; ?>" value="<a href='<?php echo URL; ?>/file.php?id=<?php echo $file_unique_id; ?>'><img src='<?php echo STACKPATH_URL ."/". $file_url; ?>' /></a>" />
									<div class="input-group-append">
										<button class="btn btn-primary btn-copy" data-clipboard-target="#html_link_<?php echo $file_unique_id; ?>" type="button" id="button-addon2">Copy</button>
									</div>
								</div>
							</div>
							<?php
							}
							?>
							
							<?php
							if($show_forum_code == 1) { 	
							?>
							<div class="form-group">
								<label>IMG Link</label>
								<div class="input-group input-group-copy-link-small">
									<input type="text" class="form-control" id="img_link_<?php echo $file_unique_id; ?>" value="[IMG]<?php echo STACKPATH_URL ."/". $file_url; ?>[/IMG]" />
									<div class="input-group-append">
										<button class="btn btn-primary btn-copy" data-clipboard-target="#img_link_<?php echo $file_unique_id; ?>" type="button" id="button-addon2">Copy</button>
									</div>
								</div>
							</div>
							<?php
							}
							?>
							
							<?php
							if($show_social_share == 1) { 	
							?>
							<div class="form-group">
								<div class="shareIcons"></div>
							</div>
							<?php
							}
							?>
						</div>
						<?php
						}
						?>
					</div>
				</div>
			</div>
			<?php
			} // endwhile
		}
        
		
		/*
		$result["files"] = $get_file_infos->fetchAll();
		$result["status"] = 1;
		*/
	}
	
} else {
	
	echo "";
	
}
?>