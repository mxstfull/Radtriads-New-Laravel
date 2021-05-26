<?php
include("templates/headers/inc.php");

// Include the JS file
$js_files = array("");
$css_files = array("");

// Metadata informations of this page
$page_slug	= "admin";

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
	header("Location: error?action=forbidden");
	exit;	
}

// -- Include the header template
include("templates/headers/admin_header.php");

// Count total users in the DB
$user_total_sql = $dbh->prepare("SELECT id FROM user");
$user_total_sql->execute();
$total_users = $user_total_sql->rowCount();

// Count total users in current month
$user_month_sql = $dbh->prepare("SELECT id FROM user WHERE MONTH(created_at) = MONTH(CURRENT_DATE())");
$user_month_sql->execute();
$month_users = $user_month_sql->rowCount();

// Count total photos in the DB
$photo_total_sql = $dbh->prepare("SELECT id FROM file");
$photo_total_sql->execute();
$total_photos = $photo_total_sql->rowCount();

// Count photos in current month
$photo_month_sql = $dbh->prepare("SELECT id FROM file WHERE MONTH(created_at) = MONTH(CURRENT_DATE())");
$photo_month_sql->execute();
$month_photos = $photo_month_sql->rowCount();

$now = new \DateTime('now');
$month = $now->format('F');
?>

<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Dashboard</h1>
</div>

<!-- Content Row -->
<div class="row">
	<div class="col-xl-3 col-md-6 mb-4">
	    <div class="card border-left-primary shadow h-100 py-2">
	        <div class="card-body">
	            <div class="row no-gutters align-items-center">
	                <div class="col mr-2">
	                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Files</div>
	                    <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $total_photos; ?></div>
	                </div>
	                <div class="col-auto">
	                    <i class="fas fa-camera fa-2x text-gray-300"></i>
	                </div>
	            </div>
	        </div>
	    </div>
	</div>

	<div class="col-xl-3 col-md-6 mb-4">
	    <div class="card border-left-primary shadow h-100 py-2">
	        <div class="card-body">
	            <div class="row no-gutters align-items-center">
	                <div class="col mr-2">
	                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Files in <?php echo $month; ?></div>
	                    <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $month_photos; ?></div>
	                </div>
	                <div class="col-auto">
	                    <i class="fas fa-calendar fa-2x text-gray-300"></i>
	                </div>
	            </div>
	        </div>
	    </div>
	</div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Users</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $total_users; ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-users fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Users in <?php echo $month; ?></div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $month_users; ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-calendar fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4 second-admin-header">
    <h1 class="h3 mb-0 text-gray-800">Admin Shortcuts</h1>
</div>

<!-- Content Row -->
<div class="row lst-shortcuts">
	<!--
	<div class="col-lg-6 mb-4">
		 <a href="manage-settings.php">
	        <div class="card bg-primary text-white shadow">
	            <div class="card-body">
	                Website Settings
	                <div class="text-white-50 small">Change website name, tagline or other general settings...</div>
	            </div>
    		</div>
        </a>
    </div>
    -->
    
    <div class="col-lg-6 mb-4">
		 <a href="manage-users">
	        <div class="card bg-primary text-white shadow">
	            <div class="card-body">
	                Manage Users
	                <div class="text-white-50 small">List users, edit or delete them...</div>
	            </div>
    		</div>
        </a>
    </div>
    
    <div class="col-lg-6 mb-4">
		 <a href="manage-photos">
	        <div class="card bg-primary text-white shadow">
	            <div class="card-body">
	                Manage Files
	                <div class="text-white-50 small">List files or delete them...</div>
	            </div>
    		</div>
        </a>
    </div>
    
    <div class="col-lg-6 mb-4">
		 <a href="manage-pages">
	        <div class="card bg-primary text-white shadow">
	            <div class="card-body">
	                Manage Pages
	                <div class="text-white-50 small">List pages, edit or delete them...</div>
	            </div>
    		</div>
        </a>
    </div>
</div>
<?php
// -- Include the footer template
include("templates/footers/admin_footer.php");	
?>