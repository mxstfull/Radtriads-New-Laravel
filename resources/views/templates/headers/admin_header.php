<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="description" content="">
        <meta name="author" content="">
        <title><?php echo $page_title; ?></title>
        <!-- Custom fonts for this template-->
        <link href="fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
        
        <!-- <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css"> -->
        <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
        <!-- Custom styles for this template-->
        <link href="css/sb-admin-2.min.css" rel="stylesheet">
        <link href="css/jquery.toast.min.css" rel="stylesheet">
        <link href="css/custom.css?v=<?php echo mt_rand(0,99999); ?>" rel="stylesheet">
        <link href="css/admin.css?v=<?php echo mt_rand(0,99999); ?>" rel="stylesheet">
        
        <?php
	    foreach($css_files as $css_f) {  
	    ?>
		<link href="<?php echo $css_f; ?>?v=<?php echo mt_rand(0,99999); ?>" rel="stylesheet">
        <?php
	    }
	    ?>
        
    </head>
    <body id="page-top">
        <!-- Page Wrapper -->
        <div id="wrapper">
            <!-- Sidebar -->
            <ul class="navbar-nav bg-gradient-primary admin-view sidebar sidebar-dark accordion" id="accordionSidebar">
                <!-- Sidebar - Brand -->
                <a class="sidebar-brand d-flex align-items-center justify-content-center" href="admin">
                    <div class="sidebar-brand-icon rotate-n-15">
                        <i class="fas fa-lock"></i>
                    </div>
                    <div class="sidebar-brand-text mx-3"><?php echo $website_name; ?></div>
                </a>
                <!-- Divider -->
                <hr class="sidebar-divider my-0">
                <!-- Nav Item - Dashboard -->
                <li class="nav-item <?php if($page_slug == "admin") { ?>active<?php } ?>">
                    <a class="nav-link" href="admin">
                    <i class="fas fa-fw fa-tachometer-alt"></i>
                    <span>Dashboard</span></a>
                </li>
                <!-- Divider -->
                <hr class="sidebar-divider">
                
                <li class="nav-item <?php if($page_slug == "manage_logo") { ?>active<?php } ?>">
                    <a class="nav-link" href="manage-logo">
                    <i class="fas fa-fw fa-image"></i>
                    <span>Logo</span></a>
                </li>
               
                <li class="nav-item <?php if($page_slug == "manage_users") { ?>active<?php } ?>">
                    <a class="nav-link" href="manage-users">
                    <i class="fas fa-fw fa-users"></i>
                    <span>Users</span></a>
                </li>

                <li class="nav-item <?php if($page_slug == "manage_photos") { ?>active<?php } ?>">
                    <a class="nav-link" href="manage-photos">
                    <i class="fas fa-fw fa-table"></i>
                    <span>Files</span></a> 
                </li>

                <li class="nav-item <?php if($page_slug == "manage_pages") { ?>active<?php } ?>">
                    <a class="nav-link" href="manage-pages">
                    <i class="fas fa-fw fa-folder"></i>
                    <span>Pages</span></a>
                </li>
                <!-- Divider -->
                <hr class="sidebar-divider d-none d-md-block">
                <!-- Sidebar Toggler (Sidebar) -->
                <div class="text-center d-none d-md-inline">
                    <button class="rounded-circle border-0" id="sidebarToggle"></button>
                </div>
            </ul>
            <!-- End of Sidebar -->
            <!-- Content Wrapper -->
            <div id="content-wrapper" class="d-flex flex-column">
                <!-- Main Content -->
                <div id="content">
                    <!-- Topbar -->
                    <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
                        <!-- Sidebar Toggle (Topbar) -->
                        <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                        <i class="fa fa-bars"></i>
                        </button>
                        <!-- Topbar Search -->
                        <a href="<?php echo FRONTEND_URL ?>" class="btn btn-primary back-to-website-btn">Back to Website</a>
                        <!-- Topbar Navbar -->
                        <ul class="navbar-nav ml-auto">
                           
                            <!-- Nav Item - User Information -->
                            <li class="nav-item dropdown no-arrow">
                                <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="mr-2 d-none d-lg-inline text-gray-600 small">Logged in as : <b><?php echo ucfirst($_SESSION["USERNAME"]); ?></b></span>
                                </a>
                                <!-- Dropdown - User Information -->
                                <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="userDropdown">
                                    <a class="dropdown-item" href="<?php echo FRONTEND_URL ?>">
                                    <i class="fas fa-arrow-left fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Back to Website
                                    </a>
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item" href="logout">
                                    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Logout
                                    </a>
                                </div>
                            </li>
                        </ul>
                    </nav>
                    <!-- End of Topbar -->
                    <!-- Begin Page Content -->
                    <div class="container-fluid">