<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="description" content="User friendly image and video hosting & sharing on web and mobile. Privacy controlled by you. Dynamic resizing, cropping on site. Social Media sharing. Made by users for users.">
        <meta name="keywords" content="photobucket,Imgur,vgy.me,flickr,postimage,free image hosting,ultraimg,smugmug,imgbox,image hosting,free image hosting,photo storage,photo sharing,upload photo,image share,pic upload,photo sharing website,photo websites,online photo storage,image hosting sites,Video hosting sites,photo sharing sites,free image hosting sites,picture hosting,photo hosting sites,photo sites,photo upload sites,photo hosting,photo storage sites,photobucket app,image upload site,share photos online,picture sites,free photo sharing websites,free photo sharing,host image online">
        <meta name="author" content="">
        <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
		<link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
		<link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
		<link rel="manifest" href="/site.webmanifest">
		<meta name="msapplication-TileColor" content="#da532c">
		<meta name="theme-color" content="#ffffff">
        
        <?php
	    if(isset($show_og_meta)) {
	    ?>
        <meta property="og:url"                content="<?php echo $og_meta_url; ?>" />
		<meta property="og:type"               content="website" />
		<meta property="og:title"              content="<?php echo $og_title; ?>" />
		<meta property="og:description"        content="User friendly image and video hosting & sharing on web and mobile. Privacy controlled by you. Dynamic resizing, cropping on site." />
		<meta property="og:image"              content="<?php echo $og_image; ?>" />
		<?php
		}	
		?>       
		 
		        
        <title>RadTriads Image and Video Hosting<?php if($page_title != ""): ?>- <?php echo $page_title; ?><?php endif; ?></title>
        <!-- Custom fonts for this template-->
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.2/css/all.min.css" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css?family=Righteous&display=swap" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
        <!-- Custom styles for this template-->
        <link href="css/sb-admin-2.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/fancyapps/fancybox@3.5.7/dist/jquery.fancybox.min.css" />
		<link href="https://vjs.zencdn.net/7.8.4/video-js.css" rel="stylesheet" />
        <link href="css/jquery.multi-draggable.css" rel="stylesheet">
        
        <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDkGP4XbpCDaAB-qFIqnJqNIqStWWA1IOU&callback=initAutocomplete&libraries=places&v=weekly" defer></script>
         
        <?php
	    if(isset($css_files)) {
		    foreach($css_files as $css_f) {  
		    ?>
			<link href="<?php echo $css_f; ?>?v=<?php echo mt_rand(0,99999); ?>" rel="stylesheet">
	        <?php
		    }
	    }
	    ?>
        
        <link href="css/custom.css?v=<?php echo mt_rand(0, 99999); ?>" rel="stylesheet">
    </head>
    <body class="bg-gradient-primary">
	    <?php
		$has_topbar = false;
		if(isset($_GET["action"])) {
			$has_topbar = true;
			if($_GET["action"] == "album_created") {
				
			?>
			<div class="topbar success">
				Awesome. Your album has been created !
			</div>
			<?php
			} else if($_GET["action"] == "files_copied") {
			?>
			<div class="topbar success">
				Done. Your files have been copied.
			</div>
			<?php
			} else if($_GET["action"] == "files_moved") {
			?>
			<div class="topbar success">
				Done. Your files have been moved.
			</div>
			<?php
			} else if($_GET["action"] == "files_deleted") {
			?>
			<div class="topbar success">
				Done. Your files have been deleted.
			</div>
			<?php
			} else if($_GET["action"] == "file_deleted") {
			?>
			<div class="topbar success">
				Done. This file has been deleted.
			</div>
			<?php
			} else if($_GET["action"] == "files_recovered") {
			?>
			<div class="topbar success">
				Done. Your files have been recovered.
			</div>
			<?php
			} else if($_GET["action"] == "album_deleted") {
			?>
			<div class="topbar success">
				Done. Your album has been deleted.
			</div>
			<?php
			} else if($_GET["action"] == "no_files_download") {
			?>
			<div class="topbar success">
				Oops. No files to download in this album.
			</div>
			<?php
			} else if($_GET["action"] == "uploaded") {
			?>
			<div class="topbar success">
				Awesome. The upload was a success!
			</div>
			<?php
			} else if($_GET["action"] == "need_renew_not_active") {
			?>
			<div class="topbar danger">
				Oops. Seems like your subscription has expired or has been cancelled. Please renew below.
			</div>
			<?php
			} else if($_GET["action"] == "invalid_verification_code") {
			?>
			<div class="topbar danger">
				Oops. This verification code is invalid or you have already verified your account.
			</div>
			<?php
			} else if($_GET["action"] == "pick_plan") {
			?>
			<div class="topbar danger">
				Oops. Please pick a plan below.
			</div>
			<?php
			} else if($_GET["action"] == "code_validated") {
			?>
			<div class="topbar success">
				Awesome! Your account has been verified and you can now log-in below.
			</div>
			<?php
			} else if($_GET["action"] == "download_soon_ready") {
			?>
			<div class="topbar success">
				Awesome! We are preparing your download and it will be sent to your email in 30 minutes max.
			</div>
			<?php
			}
		}  
		?>
        <div class="<?php if($has_topbar): ?>hastopbar<?php endif; ?>">
	        
			<div class="top_header clearfix">
				
				<?php
			    if(isset($_SESSION["RANK"]) && $_SESSION["RANK"] > 0) {
				?>
		        <a href="admin.php" class="admin-link"><span>Admin</span></a>
				<?php
			    }  
			    ?>
			
				<div class="container">
					
					<div class="logo">
				        <a href="index.php"><img src="<?php echo $website_logo; ?>" /></a>
			        </div>
			        
			        <div class="mobile-menu">
				        <a href="#"><i class="fas fa-bars"></i> Menu</a>
			        </div>
			        
			        <div class="menu-right">
				        <?php
					    if($_SESSION) {
						?>
						<a href="dashboard.php" <?php if($page_slug == "dashboard") { ?>class="active"<?php } ?>><span>Home</span></a>
						<a href="recent-uploads.php" <?php if($page_slug == "recent_uploads") { ?>class="active"<?php } ?>><span>Recent Uploads</span></a>
						<?php
						} else {  
					    ?>
				        <a href="index.php" <?php if($page_slug == "index") { ?>class="active"<?php } ?>><span>Home</span></a>
				        <?php
					    }
					    ?>
				        <?php
					    if(!$_SESSION) {  
						?>
					        <a href="sign-in.php" <?php if($page_slug == "sign_in") { ?>class="active"<?php } ?>><span>Sign In</span></a>
					        <a href="sign-up.php" <?php if($page_slug == "sign_up") { ?>class="active"<?php } ?>><span>Sign Up</span></a>
					        <?php
						    } else {
							?>
					        <a href="#" class="dropdown-toggle" type="button" id="dropdownMenu2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
						        <span>My Account</span>
								<div class="dropdown-menu" aria-labelledby="dropdownMenu2">
									<button class="dropdown-item" data-url="my-account.php?tab=my_infos" type="button">My Info</button>
									<button class="dropdown-item" data-url="my-account.php?tab=plan" type="button">Plan & Usage</button>
									<button class="dropdown-item" data-url="my-account.php?tab=settings" type="button">Settings</button>
									<button class="dropdown-item" data-url="my-account.php?tab=security" type="button">Privacy & Security</button>
									<button class="dropdown-item" data-url="my-account.php?tab=password" type="button">Password</button>
									<button class="dropdown-item dropdown-usage" type="button">
										<h5>Disk Space Usage</h5>
										<div class="progress">
											<div class="progress-bar" role="progressbar" style="width: <?php echo $percent_diskspace; ?>%;" aria-valuenow="<?php echo $percent_diskspace; ?>" aria-valuemin="0" aria-valuemax="100">
												<div class="percent_val"><?php echo $percent_diskspace; ?>%</div>
											</div>
										</div>
									</button>
									<!--
									<button class="dropdown-item dropdown-usage" type="button">								
										<h5>Bandwidth Usage</h5>
										<div class="progress">
											<div class="progress-bar" role="progressbar" style="width: <?php echo $percent_bandwidth; ?>%;" aria-valuenow="<?php echo $percent_bandwidth; ?>" aria-valuemin="0" aria-valuemax="100">
												<div class="percent_val"><?php echo $percent_bandwidth; ?>%</div>
											</div>
										</div>
									</button>
									-->
								</div>
						    </a>
					        
					        <a href="logout.php" class="logout-link"><span>Logout</span></a>
						<?php
					    }
					    ?>
					    
					    <div id="dash-acts-container" class="clearfix">
						    
					    </div>
			        </div>
		        
				</div>
		        
	        </div>
	        
		    
		    <div class="container main-container">