<?php
include("templates/headers/inc.php");
include("templates/headers/calculate_usage.php");

// Include the JS file
$js_files = array();

// Metadata informations of this page
$page_slug	= "";

// Get website config
$site_config = $dbh->prepare("SELECT * FROM config WHERE config_name IN ('website_logo','website_name','website_tagline','ads_code','analytics_code','allow_button','allow_drag','allow_webcam')");
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
$website_logo = $config_array["website_logo"];

if(!isset($_GET["id"])) {
	header("Location: error?action=forbidden");
	exit;		
}

$page_id = intval($_GET["id"]);

// Check if user exists...
$page_sql = $dbh->prepare("	SELECT *
							FROM custom_page
							WHERE id = :page_id
							");
					
$page_sql->bindParam(':page_id', $page_id);
$page_sql->execute();
	
$page = $page_sql->fetch();


$title_page = $page["title"];
$content_page = $page["content"];

$page_title = $title_page;

// -- Include the header template
include("templates/headers/index_header.php");
?>

<!-- Outer Row -->
<div class="row justify-content-center main_uploader_block">
    <div class="col-xl-12 col-lg-12 col-md-9">
        <div class="card o-hidden border-0 shadow-lg my-5">
            <div class="card-body p-0">
                <!-- Nested Row within Card Body -->
                <div class="row">
                    <div class="col-lg-12">
                        <div class="p-5">
                            <div class="text-center upload-container">
	                            <div class="alert alert-danger alert-error-upload">
	                            </div>
                                <h1 class="text-gray-900"><?php echo $title_page; ?></h1>
								
								<div class="row row_sign_up">
																		
									<div class="col-md-12 page-content">
										
										<?php echo $content_page; ?>
										
									</div>
									
								</div>
								
                            </div>                           
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// -- Include the footer template
include("templates/footers/global_footer.php");	
?>