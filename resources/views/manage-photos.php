<?php
include("templates/headers/inc.php");
include("includes/global.php");
// Include the JS file
$js_files = array("js/bootbox.all.min.js", "js/masonry.pkgd.min.js", "js/imagesloaded.pkgd.min.js", "js/pages/manage_photos.js");
$css_files = array("");

// Metadata informations of this page
$page_slug	= "manage_photos";

// Get website config
$site_config = $dbh->prepare("SELECT * FROM config WHERE config_name IN ('website_name','website_tagline','ads_code','analytics_code','allow_button','allow_drag','allow_webcam')");
$site_config->execute();

$config_array = array();

while($config = $site_config->fetch(PDO::FETCH_ASSOC)) {
	$config_array[$config["config_name"]] = $config["config_value"];
}

$website_name = $config_array["website_name"];
$website_tagline = $config_array["website_tagline"];
$ads_code = $config_array["ads_code"];
$analytics_code = $config_array["analytics_code"];
$allow_button = $config_array["allow_button"];
$allow_drag = $config_array["allow_drag"];
$allow_webcam = $config_array["allow_webcam"];

$page_title = $website_name . " - " . $website_tagline;

if($_SESSION["RANK"] == 0) {
	header("Location: index.php?action=forbidden");
	exit;	
}

$user_id = $_SESSION["USER_ID"];

$total_users = $dbh->prepare("	SELECT *
								FROM user
								ORDER BY created_at DESC
								");
					
$total_users->execute();

// -- Include the header template
include("templates/headers/admin_header.php");

?>


<!-- Content Row -->
<div class="row">
	
	<div class="col-xl-12 manage-photos">
		<?php
		// Get all the photos
		$photos_query = $dbh->prepare("SELECT file.* FROM file LEFT JOIN album ON album.path = file.folder_path ORDER BY file.id DESC LIMIT 60");
		$photos_query->execute();
		
		if($photos_query->rowCount() == 0) {
		?>
		<div class="col-md-12">
			<div class="alert alert-danger alert-center">
				No files have been uploaded for the moment.
			</div>
		</div>
		<?php
		}	
		?>
		
		<div class="grid-container">
			<div class="row grid">
				
				
				<?php
				while($photo = $photos_query->fetch(PDO::FETCH_ASSOC)) {
					$is_picture = $photo["is_picture"];
				?>
				
					
				<div class="col-md-2 card-photo" data-id="<?php echo $photo["id"]; ?>">
					
					<div class="card shadow">
				
						<div class="card-photo-container">
							
							
							<?php
							if($is_picture == 1) {
							?>
								<a href="<?php echo FRONTEND_URL ?>/photo-details?id=<?php echo $photo["unique_id"]; ?>">
								<img src="<?php echo URL; ?>/files/<?php echo my_laravelEncode($photo["thumb_url"]); ?>" class="card-img-top" alt="">					
							<?php
							} else {
							?>
								<a target="blank" href="<?php echo URL; ?>/files/<?php echo my_laravelEncode($photo["url"]); ?>">
								<img class="card-img-top" src="img/file_2.png" />
							<?php
							}
							?>
							</a>					
						
						</div>
						
						<div class="card-body">
							<h5 class="card-title"><?php echo $photo["title"]; ?></h5>
							<h5 class="photo_date"><?php echo $photo["created_at"]; ?><h5>
							<h6>
								<?php
								if($photo["is_protected"] == 1) {
								?>
								<span class="badge badge-secondary badge-privacy">Private</span>
								<?php
								} else if($photo["is_protected"] == 2) {
								?>
								<span class="badge badge-warning badge-privacy">Password Protected</span>
								<?php
								} else {
								?>
								<span class="badge badge-success badge-privacy">Public</span>
								<?php
								}
								?>
							</h6>
						    <div class="row no-gutters">
							    <div class="col-md-12">
								    <a href="#" class="btn btn-danger btn-block btn-delete"><i class="fas fa-times"></i> Delete</a>
							    </div>
						    </div>
						</div>
					
					</div>
				
				</div>
				
				
				<?php
				}	
				?>
					
			</div>
		</div>
	</div>
	
</div>	    

<script type="text/javascript">

var url = "<?php echo URL; ?>";
var frontend_url = "<?php echo FRONTEND_URL; ?>";	
</script>
<?php
// -- Include the footer template
include("templates/footers/admin_footer.php");	
?>