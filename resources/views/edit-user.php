<?php
include("templates/headers/inc.php");

// Include the JS file
$js_files = array("js/summernote-bs4.min.js", "js/pages/add-page.js");
$css_files = array("css/summernote-bs4.css?v=1");

// Metadata informations of this page
$page_slug	= "edit_user";

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

if(!isset($_GET["id"])) {
	header("Location: error?action=forbidden");
	exit;		
}

$success = "";

$user_id = intval($_GET["id"]);

$user_infos = $dbh->prepare("SELECT u.email, u.username, u.id, u.unique_id, u.stripe_plan, p.diskspace AS max_diskspace, p.bandwidth AS max_bandwidth, show_social_share, show_direct_link, show_forum_code, show_html_code, stripe_plan_admin FROM user u, plan p WHERE u.id = :user_id AND u.plan_id = p.id");
$user_infos->bindParam(":user_id", $user_id);
$user_infos->execute();

$user = $user_infos->fetch();
$max_diskspace = $user["max_diskspace"];
$max_bandwidth = $user["max_bandwidth"];

// Get total bandwidth used
$user_usage_used = $dbh->prepare("SELECT 
								  SUM(bandwidth) AS month_bandwidth,
								  SUM(diskspace) AS month_diskspace
								  FROM file 
								  WHERE 
								  user_id = :user_id");
$user_usage_used->bindParam(":user_id", $user_id);
$user_usage_used->execute();
									  
if($user_usage_used->rowCount() > 0) {
	
	$user_usage = $user_usage_used->fetch();
	$user_diskspace = get_mb($user_usage["month_diskspace"], 3);
	$user_bandwidth = get_mb($user_usage["month_bandwidth"], 3);
		
} else {
	
	$user_diskspace = 0;
	$user_bandwidth = 0;
	
}

if($max_diskspace == 0) {
	$percent_diskspace = INF;
} else {
	$percent_diskspace = ($user_diskspace / $max_diskspace) * 100;
	$percent_diskspace = round($percent_diskspace, 2);
}

if($max_bandwidth == 0) {
	$percent_bandwidth = INF;
} else {
	$percent_bandwidth = ($user_bandwidth / $max_bandwidth) * 100;
}
	
if(is_nan($percent_diskspace)) { 
	$percent_diskspace = INF;
}

$email = $user["email"];
$stripe_plan_admin = $user["stripe_plan_admin"];
$username = $user["username"];

$get_files_query = $dbh->prepare("SELECT id FROM file WHERE user_id = :user_id");
$get_files_query->bindParam(":user_id", $user_id);
$get_files_query->execute();

$nb_files_user = $get_files_query->rowCount();

$page_title = "Edit user : " . $username;

if($_SESSION["RANK"] == 0) {
	header("Location: index.php?action=forbidden");
	exit;	
}

$error = "";


if($_POST) {
	
	if(TEST_MODE) {
		$error = "You can't edit a page in demo mode.";
	} else {
		
		if(isset($_POST["username"]) && isset($_POST["email"])) {
			
			$username = $_POST["username"];
			$email = $_POST["email"];
			$stripe_plan_admin = $_POST["user_plan_config"];
			
			$plan_id = 0;
			
			if($stripe_plan_admin == "silver") {
				
				$plan_id = 1;
				
			} else if($stripe_plan_admin == "gold") {
				
				$plan_id = 2;
				
			} else if($stripe_plan_admin == "platinum") {
				
				$plan_id = 3;
			
			}
			
			if(empty($username) || empty($email)) {
				
				$error = "Please fill the form.";
				
			} else if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
						
				$error = "Please provide a valid email address...";
						
			} else {
				
				if($plan_id == 0) {
								
					$site_config = $dbh->prepare("UPDATE user SET stripe_plan_admin = NULL, username = :username, email = :email WHERE id = :user_id");
					$site_config->bindParam(":username", $username);
					$site_config->bindParam(":email", $email);
					$site_config->bindParam(":user_id", $user_id);
					$site_config->execute();
				
				} else {
					
					$site_config = $dbh->prepare("UPDATE user SET plan_id = :plan_id, username = :username, email = :email, stripe_plan_admin = :stripe_plan_admin WHERE id = :user_id");
					$site_config->bindParam(":username", $username);
					$site_config->bindParam(":email", $email);
					$site_config->bindParam(":plan_id", $plan_id);
					$site_config->bindParam(":stripe_plan_admin", $stripe_plan_admin);
					$site_config->bindParam(":user_id", $user_id);
					$site_config->execute();

					
				}
				
				$success = "The user has been updated!";
				
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
    <h1 class="h3 mb-0 text-gray-800">View / Edit User [<?php echo $username; ?>]</h1>
</div>

<!-- Content Row -->
<div class="row">
	
	<?php 
	if($success != "") {
	?>
	<div class="col-xl-12">
		<div class="alert alert-success text-center">
			<?php echo $success; ?>
		</div>
	</div>
	<?php
	}	
	?>
	
	<div class="col-xl-12">
		<div class="card shadow mb-4">
			<div class="card-header py-3">
			    <h6 class="m-0 font-weight-bold text-primary">User Infos & Usage</h6>
			</div>
			<div class="card-body">
				
				<div class="row row_sign_up" style="margin-top: 0;">
                    <div class="col-md-12">
                        <h5>Files Count</h5>
                        
                        <p style="margin-bottom: 0;">
                            This user has uploaded <strong><?php echo $nb_files_user; ?></strong> files in his account.
                        </p>
                    </div>
                </div>
				<hr />

				<div class="row">               
                    <div class="col-md-6">
                        
                        <h5>Bandwidth Usage</h5>
                        
						<div class="progress">
							<div class="progress-bar" role="progressbar" style="width: <?php echo INF; ?>%;" aria-valuenow="<?php echo $percent_bandwidth; ?>" aria-valuemin="0" aria-valuemax="100">
								<div class="percent_val"><?php echo INF; ?>%</div>
								
							</div>
						</div>
						<?php echo $user_bandwidth; ?> / <?php echo 0; ?> MB
                    </div>
                    <div class="col-md-6">
                        <h5>Disk Space Usage</h5>
						<div class="progress">
							<div class="progress-bar" role="progressbar" style="width: <?php echo $percent_diskspace; ?>%;" aria-valuenow="<?php echo $percent_diskspace; ?>" aria-valuemin="0" aria-valuemax="100">
								<div class="percent_val"><?php echo $percent_diskspace; ?>%</div>
							</div>
						</div>
						<?php echo $user_diskspace; ?> MB / <?php echo $max_diskspace; ?> MB
                    </div>
                </div>
                
                <hr />
                
                <div class="text-center">
	            	<a class="btn btn-primary" href="view-user-files?id=<?php echo $user_id; ?>"><i class="fas fa-eye"></i> View User Files (<?php echo $nb_files_user; ?>)</a>    
                </div>
				
			</div>
		</div>
		<div class="card shadow mb-4">
			<div class="card-header py-3">
			    <h6 class="m-0 font-weight-bold text-primary">Edit User</h6>
			</div>
			<div class="card-body">
				
				<form method="POST" action="">
					<div class="form-group">
				    	<label class="control-label" for="inputTitle">Username</label>
						<div class="controls">
							<input type="text" id="inputTitle" placeholder="Write the username of the user" class="form-control" name="username" value="<?php echo $username; ?>">
						</div>
				    </div>
				    <div class="form-group">
				    	<label class="control-label" for="inputContent">Email</label>
						<div class="controls">
							<input type="email" id="inputTitle" placeholder="Write the email of the user" class="form-control" name="email" value="<?php echo $email; ?>">
						</div>
				    </div>
				    <div class="form-group">
				    	<label class="control-label" for="inputContent">Override user plan</label>
						<div class="controls">
							<select class="form-control" name="user_plan_config">
								<option value="0" <?php if($stripe_plan_admin == 0): ?>selected<?php endif; ?>>Default user plan configuration</option>
								<option value="silver" <?php if($stripe_plan_admin == "silver"): ?>selected<?php endif; ?>>Silver Plan</option>
								<option value="gold" <?php if($stripe_plan_admin == "gold"): ?>selected<?php endif; ?>>Gold Plan</option>
								<option value="platinum" <?php if($stripe_plan_admin == "platinum"): ?>selected<?php endif; ?>>Platinum Plan</option>
							</select>
						</div>
						<small>"<strong>Default user plan configuration</strong>" will keep the plan the user selected by himself. Select "<strong>silver</strong>", "<strong>gold</strong>" or "<strong>premium</strong>" if you want to set the user to a specific plan manually.</small>
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
						<button type="submit" class="btn btn-primary btn-edit-config">Edit User <i class="fas fa-check"></i></button>
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