<?php
include("templates/headers/inc.php");
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Redirect;
// Include the JS file
$js_files = array("js/summernote-bs4.min.js", "js/pages/add-page.js");
$css_files = array("css/summernote-bs4.css?v=1");

// Metadata informations of this page
$page_slug	= "manage_pages";

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

$user_id = $_SESSION["USER_ID"];
$error = "";


if($_POST) {
	
	if(TEST_MODE) {
		$error = "You can't add a page in demo mode.";
	} else {
		
		if(isset($_POST["title"]) && isset($_POST["content"])) {
			
			$title_page = $_POST["title"];
			$content_page = $_POST["content"];
			
			if(empty($title_page) || empty($content_page)) {
				
				$error = "Please fill the form.";
				
			} else {
				
				$site_config = $dbh->prepare("INSERT INTO custom_page SET title = :title, content = :content, user_id = :user_id, date = NOW(), status = 1");
				$site_config->bindParam(":title", $title_page);
				$site_config->bindParam(":content", $content_page);
				$site_config->bindParam(":user_id", $user_id);
				$site_config->execute();
				view('manage-photos');
				// Redirect::route('manage-pages?action=added');
				// Redirect::to('/manage-pages?action=added');
				// header("Location: admin");
				
			}
			
		} else {
			
			$error = "Please fill the form.";
			
		}
	
	}
	
}

// -- Include the header template
include("templates/headers/admin_header.php");

?>

<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Manage Pages</h1>
    <a href="add-page" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm"><i class="fas fa-plus fa-sm text-white-50"></i> Add Page</a>
</div>

<!-- Content Row -->
<div class="row">
	
	<div class="col-xl-12">
		<div class="card shadow mb-4">
			<div class="card-header py-3">
			    <h6 class="m-0 font-weight-bold text-primary">Add Page</h6>
			</div>
			<div class="card-body">
				
				<form method="POST" action="" class="form-admin">
					<div class="form-group">
				    	<label class="control-label" for="inputTitle">Title</label>
						<div class="controls">
							<input type="text" id="inputTitle" placeholder="Write the title of this custom page" class="form-control" name="title" value="">
						</div>
				    </div>
				    <div class="form-group">
				    	<label class="control-label" for="inputContent">Content</label>
						<div class="controls">
							<textarea class="form-control summernote" name="content"></textarea>
						</div>
				    </div>
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
						<button type="submit" class="btn btn-primary btn-edit-config">Add Page <i class="fas fa-check"></i></button>
					</center>
				</form>
				
			</div>
		</div>
		
		
	</div>
	
</div>	    


<?php
// -- Include the footer template
include("templates/footers/admin_footer.php");	
?>