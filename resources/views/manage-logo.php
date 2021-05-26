<?php
include("templates/headers/inc.php");
include("includes/upload.class.php");
include("includes/global.php");
// Include the JS file
$js_files = array("js/pages/manage_logo.js");
$css_files = array("");

// Metadata informations of this page
$page_slug	= "manage_settings";

// Get website config
$site_config = $dbh->prepare("SELECT * FROM config WHERE config_name IN ('website_logo', 'website_name','website_tagline','ads_code','analytics_code','allow_button','allow_drag','allow_webcam','max_upload_size','max_files_upload','auto_deletion','auto_deletion_days')");
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
$max_upload_size = $config_array["max_upload_size"];
$max_files_upload = $config_array["max_files_upload"];
$auto_deletion = $config_array["auto_deletion"];
$auto_deletion_days = $config_array["auto_deletion_days"];
$website_logo = $config_array["website_logo"];

$page_title = $website_name . " - " . $website_tagline;

if($_SESSION["RANK"] == 0) {
	header("Location: index.php?action=forbidden");
	exit;	
}

$success = 0;
$error = "";

if($_POST) {
	if(TEST_MODE) {
		$error = "You can't update the website settings in demo mode.";
	} else {
	
		if(isset($_FILES["file_logo"])) {
	
			$file = $_FILES["file_logo"];
			
			$handle = new Upload($file);
			$err_img = false;
		
			if ($handle->uploaded) {
				
				// Generate a unique name for the directory
				$unique_photo_name = sha1(time() . mt_rand(0,99999));
				
				// Generate a unique directory name
				$today_dir_name = date('Ymd');
				$complete_dir_name = 'logos/' . $today_dir_name . '/';
				if (!is_dir(storage_path('app/logos/'))) {
					mkdir(storage_path('app/logos/'));
				}
				if (!is_dir(storage_path('app/'.$complete_dir_name))) {
					mkdir(storage_path('app/'.$complete_dir_name));
				}
				
				// Parameters before uploading the photo
				$handle->image_resize         	= false;
				$handle->png_compression 		= 8;
				$handle->webp_quality 			= 80;
				$handle->jpeg_quality 			= 80;
				$handle->file_new_name_body   	= $unique_photo_name;
				$handle->allowed = array('image/*');
				
				$handle->Process(storage_path('app/'.$complete_dir_name));
				
				if ($handle->processed) {
					
					$err_img = false;
					$handle->clean();
					$has_img = true;
					
					$photo_url = $complete_dir_name.$handle->file_dst_name;
		
					$site_config = $dbh->prepare("UPDATE config SET config_value = :config_value WHERE config_name = 'website_logo'");
					$site_config->bindParam(":config_value", $photo_url);
					$site_config->execute();
					
					$website_logo = $photo_url;
					
					$success = 1;
					
					
				} else {
					$error = "An error occurred while uploading the new logo. Please check your file and try again...";
					$err_img = true;
				}
		
			} else {
				$error = "An error occurred while uploading the new logo. Please check your file and try again...";
				$err_img = true;
			}
			
		}
		
	
	}
	
}

$user_id = $_SESSION["USER_ID"];

// -- Include the header template
include("templates/headers/admin_header.php");

?>

<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Manage Logo</h1>
</div>

<?php
if($success == 1) {	
?>
<div class="alert alert-success alert-center">
	Your logo has been updated.              
</div>
<?php
}
?>
<form class="form-admin" method="POST" enctype="multipart/form-data" >
	<input type="file" name="file_logo" id="file_logo" style="display: none;" />
	<input type="hidden" name="form_sent" value="1" />
	<!-- Content Row -->
	<div class="row">
		<div class="col-xl-12">
			
		    <div class="card shadow mb-4">
		        <!-- Card Header - Dropdown -->
		        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
		            <h6 class="m-0 font-weight-bold text-primary">Logo</h6>
		            
		        </div>
		        <!-- Card Body -->
		        <div class="card-body">
					<div class="row">
						<div class="col-md-12">
							<div class="form-group">
								<label class="text-gray-900">Current Logo</label>
								
								<img class="adm-website-logo" src="<?php echo URL; ?>/files/<?php echo my_laravelEncode($website_logo); ?>" />
								
								<p class="info"><small><i class="fas fa-info-circle"></i> Click on the current logo to upload a new one...</small></p>
							</div>
						</div>
						
					</div>
		        </div>
		    </div>
		</div>
		
	</div>
	
	<div class="row">
		<div class="col-xl-12">
									
			<?php
			if($error != "") {	
			?>
			<div class="alert alert-danger alert-center">
				<?php echo $error; ?>              
		    </div>
		    <?php
			}
			?>
			<center>
				<button type="submit" class="btn btn-primary btn-edit-config">Save Config <i class="fas fa-check"></i></button>
			</center>
		</div>
	</div>
</form>

<?php
// -- Include the footer template
include("templates/footers/admin_footer.php");	
?>