<?php
include("templates/headers/inc.php");

// Include the JS file
$js_files = array("js/bootbox.all.min.js", "js/pages/manage_pages.js");
$css_files = array("");

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

$total_pages = $dbh->prepare("	SELECT *
								FROM custom_page
								ORDER BY date DESC
								");
					
$total_pages->execute();

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
		<?php
		if(isset($_GET["action"])) {
			
			$action = $_GET["action"];
			
			if($action == "added") {
			?>
			<div class="alert alert-success alert-center">
				The page was successfully added!
			</div>
			<?php
			} else if($action == "edited") {
			?>
			<div class="alert alert-success alert-center">
				The page was successfully edited!
			</div>
			<?php
			}
		}
		?>
		<div class="card shadow mb-4">
			<div class="card-header py-3">
			    <h6 class="m-0 font-weight-bold text-primary">Pages List</h6>
			</div>
			<div class="card-body">
				<?php
				if($total_pages->rowCount() == 0) {
				?>
				<div class="alert alert-danger alert-center">
					No pages are created on the platform for the moment.
				</div>
				<?php
				} else {
				?>
				<div class="table-responsive">
					<table class="table table-bordered"
						<tr>
							<th>Page Name</th>
							<th>Actions</th>
						</tr>
						<?php
						// Pagination
						$perpage = 15;
						$posts  = $total_pages->rowCount();
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
						$pages_query = $dbh->prepare("SELECT * FROM custom_page ORDER BY date DESC LIMIT :limit, :perpage");
						$pages_query->bindParam(':perpage', $perpage, PDO::PARAM_INT);
						$pages_query->bindParam(':limit', $range, PDO::PARAM_INT);
						$pages_query->execute();
							
						while($page = $pages_query->fetch(PDO::FETCH_ASSOC)) {					
						?>
						<tr class="page-line" data-id="<?php echo $page["id"]; ?>">
							<td style="width: 80%;vertical-align: middle;">
								<?php echo $page["title"]; ?>
							</td>
							<td>
								<div class="row">
									<div class="col-md-4">
										<a class="btn btn-dark  btn-sm btn-block" href="<?php echo FRONTEND_URL; ?>/page?id=<?php echo $page["id"]; ?>"><i class="fas fa-eye"></i></a>
									</div>
									<div class="col-md-4">
										<a href="edit-page?id=<?php echo $page["id"]; ?>" class="btn btn-primary btn-admin-edit btn-sm btn-block"><i class="fas fa-pencil-alt"></i></a>
									</div>
									<div class="col-md-4">
										<a href="" class="btn btn-danger btn-delete btn-sm btn-block" data-deletion="<?php echo $page["allow_delete"]; ?>" data-id="<?php echo $page["id"]; ?>"><i class="fas fa-times"></i></a>
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

<?php
// -- Include the footer template
include("templates/footers/admin_footer.php");	
?>