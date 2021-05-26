<?php
include("templates/headers/inc.php");

// Include the JS file
$js_files = array("js/bootbox.all.min.js", "js/pages/manage_users.js");
$css_files = array("");

// Metadata informations of this page
$page_slug	= "manage_users";

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

<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Manage Users</h1>
</div>

<!-- Content Row -->
<div class="row">
	
	<div class="col-xl-12">
		<div class="card shadow mb-4">
			<div class="card-header py-3">
			    <h6 class="m-0 font-weight-bold text-primary">Users List</h6>
			</div>
			<div class="card-body">
				<?php
				if($total_users->rowCount() == 0) {
				?>
				<div class="alert alert-danger alert-center">
					No users are registered on the platform for the moment.
				</div>
				<?php
				} else {
				?>
				<div class="table-responsive">
					<table class="table table-bordered"
						<tr>
							<th>Username</th>
							<th>Email</th>
							<th>Actions</th>
						</tr>
						<?php
						// Pagination
						$perpage = 15;
						$posts  = $total_users->rowCount();
						$pages  = ceil($posts / $perpage);
						
						$get_pages = isset($_GET['page']) ? $_GET['page'] : 1;
						
						$data = array(
			
							'options' => array(
								'default'   => 1,
								'min_range' => 1,
								'max_range' => $pages
								)
						);
				
						$number = trim($get_pages);
						$number = filter_var($number, FILTER_VALIDATE_INT, $data);
						$range  = $perpage * ($number - 1);
				
						$prev = $number - 1;
						$next = $number + 1;
							
						// Get list of all quiz
						$users_query = $dbh->prepare("SELECT * FROM user ORDER BY created_at DESC LIMIT :limit, :perpage");
						$users_query->bindParam(':perpage', $perpage, PDO::PARAM_INT);
						$users_query->bindParam(':limit', $range, PDO::PARAM_INT);
						$users_query->execute();
							
						while($user = $users_query->fetch(PDO::FETCH_ASSOC)) {					
						?>
						<tr class="user-line" data-id="<?php echo $user["id"]; ?>">
							<td><?php echo ucfirst($user["username"]); ?></td>
							<td><?php echo $user["email"]; ?></td>
							<td>
								<div class="row d-flex align-items-center">
									<div class="col-md-4">
										<a data-id="<?php echo $user["id"]; ?>" data-username="<?php echo $user["username"]; ?>" data-email="<?php echo $user["email"]; ?>" data-rank="<?php echo $user["rank"]; ?>" href="view-user-files?id=<?php echo $user["id"]; ?>" class="btn btn-primary btn-sm btn-block"><i class="fas fa-file"></i> View Files</a>
									</div>
									<div class="col-md-4">
										<a data-id="<?php echo $user["id"]; ?>" data-username="<?php echo $user["username"]; ?>" data-email="<?php echo $user["email"]; ?>" data-rank="<?php echo $user["rank"]; ?>" href="edit-user?id=<?php echo $user["id"]; ?>" class="btn btn-primary btn-sm btn-block"><i class="fas fa-pencil-alt"></i> Infos & Edit</a>
									</div>
									<div class="col-md-4">
										<a href="" class="btn btn-danger btn-delete btn-delete-user btn-sm btn-block" data-id="<?php echo $user["id"]; ?>"><i class="fas fa-times"></i></a>
									</div>
								</div>
							</td>
						</tr>
						<?php
						}	
						?>
					</table>
				</div>
				<?php
				if($posts > 0) {
					if($pages > 1) {	
					?>
					<div class="pagination">
					<?php	
						echo "<div class='page-list'>";
		
						# first page
						if($number <= 1)
							echo "<span>&laquo; prev</span> | <a href=\"?page=$next\">next &raquo;</a>";
						
						# last page
						else if($number >= $pages)
							echo "<a href=\"?page=$prev\">&laquo; prev</a> | <span>next &raquo;</span>";
						
						# in range
						else
							echo "<a href=\"?page=$prev\">&laquo; prev</a> | <a href=\"?page=$next\">next &raquo;</a>";
		
						echo "</div>";
					?>
					</div>
					<?php
					}
				}	
				?>
				<?php
				}
				?>
			</div>
		</div>
		
		
	</div>
	
</div>	    

<div class="modal fade" id="edit-user-modal" tabindex="-1" role="dialog" aria-labelledby="edit-user-modal" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Edit User</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form>
	                <div class="form-group row">
				        <div class="col-sm-6 mb-3 mb-sm-0">
					        <label class="text-gray-900">Username</label>
				            <input type="text" class="form-control form-control-user user-edit-username" placeholder="Username" name="username" value="">
				        </div>
				        <div class="col-sm-6">	
					        <label class="text-gray-900">Email</label>									            
					        <input type="email" class="form-control form-control-user user-edit-email" placeholder="Email" name="email" value="">

				        </div>
				    </div>
				    
				    <div class="form-group row">
				        <div class="col-sm-12 mb-3 mb-sm-0">
					        <label class="text-gray-900">Rank</label>
				            <select class="form-control user-edit-rank">
					            <option value="0">User</option>
					            <option value="1">Admin</option>
				            </select>
				        </div>
				    </div>
				    
				    <button type="submit" class="btn btn-primary btn-confirm-edit btn-block">
				    Update Account
				    </button>
                </form>
            </div>
        </div>
    </div>
</div>
<?php
// -- Include the footer template
include("templates/footers/admin_footer.php");	
?>