<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0" />
  <meta name="description" content="POS - Bootstrap Admin Template" />
  <meta name="keywords" content="admin, estimates, bootstrap, business, corporate, creative, management, minimal, modern,  html5, responsive" />
  <meta name="author" content="Dreamguys - Bootstrap Admin Template" />
  <meta name="robots" content="noindex, nofollow" />
  <title>SE Shop PHP - Admin</title>
  <link rel="shortcut icon" type="image/x-icon" href="<?php echo APP_URL; ?>/admin/assets/img/favicon.jpg" />
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,400;0,500;0,700;1,400;1,500;1,700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="<?php echo APP_URL; ?>/admin/assets/css/bootstrap.min.css" />
  <link rel="stylesheet" href="<?php echo APP_URL; ?>/admin/assets/css/animate.css" />
  <link rel="stylesheet" href="<?php echo APP_URL; ?>/admin/assets/plugins/select2/css/select2.min.css" />
  <link rel="stylesheet" href="<?php echo APP_URL; ?>/admin/assets/css/dataTables.bootstrap4.min.css" />
  <link rel="stylesheet" href="<?php echo APP_URL; ?>/admin/assets/plugins/fontawesome/css/fontawesome.min.css" />
  <link rel="stylesheet" href="<?php echo APP_URL; ?>/admin/assets/plugins/fontawesome/css/all.min.css" />
  <link href="<?php echo APP_URL; ?>/assets/toastr/toastr.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="<?php echo APP_URL; ?>/admin/assets/css/style.css" />
</head>

<body>
  <div id="global-loader">
    <div class="whirly-loader"></div>
  </div>

  <div class="main-wrapper">
    <div class="header">
      <div class="header-left active">
        <div class="flex-fill">
          <a href="<?php echo APP_URL; ?>/admin/" class="logo">
            <img src="<?php echo APP_URL; ?>/admin/assets/img/logo.svg" alt="" />
          </a>
          <a href="<?php echo APP_URL; ?>/admin/" class="logo-small">
            <img src="<?php echo APP_URL; ?>/admin/assets/img/logo.svg" alt="" />
          </a>
        </div>
        <a id="toggle_btn" href="javascript:void(0);"> </a>
      </div>

      <a id="mobile_btn" class="mobile_btn" href="#sidebar">
        <span class="bar-icon">
          <span></span>
          <span></span>
          <span></span>
        </span>
      </a>

      <ul class="nav user-menu">
        <li class="nav-item dropdown has-arrow main-drop">
          <a href="javascript:void(0);" class="dropdown-toggle nav-link userset" data-bs-toggle="dropdown">
            <span class="user-img"><img src="<?php echo APP_URL; ?>/admin/assets/img/no-avatar-image.png" alt="" />
              <span class="status online"></span></span>
          </a>
          <div class="dropdown-menu menu-drop-user">
            <div class="profilename">
              <div class="profileset">
                <span class="user-img">
                  <img src="<?php echo APP_URL; ?>/admin/assets/img/no-avatar-image.png" alt="profile" />
                  <span class="status online"></span></span>
                <div class="profilesets">
                  <h6>John Doe</h6>
                  <h5>Admin</h5>
                </div>
              </div>
              <hr class="m-0" />
              <a class="dropdown-item" href="profile.html">
                <i class="me-2" data-feather="user"></i> My Profile</a>
              <a class="dropdown-item" href="generalsettings.html"><i class="me-2" data-feather="settings"></i>Settings</a>
              <hr class="m-0" />
              <a id="logoutButton" class="dropdown-item logout" href="javascript:void(0);">
                <i class="me-2 fas fa-sign-out-alt"></i>
                Logout
              </a>
            </div>
          </div>
        </li>
      </ul>

      <div class="dropdown mobile-user-menu">
        <a href="javascript:void(0);" class="nav-link dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false"><i class="fa fa-ellipsis-v"></i></a>
        <div class="dropdown-menu dropdown-menu-right">
          <a class="dropdown-item" href="profile.html">My Profile</a>
          <a class="dropdown-item" href="generalsettings.html">Settings</a>
          <a class="dropdown-item" href="signin.html">Logout</a>
        </div>
      </div>
    </div>

    <div class="sidebar" id="sidebar">
      <div class="sidebar-inner slimscroll">
        <div id="sidebar-menu" class="sidebar-menu">
          <ul>
            <li id="dashboardButton">
              <a id="dashboardLink" href="<?php echo APP_URL; ?>/admin/"><img src="<?php echo APP_URL; ?>/admin/assets/img/icons/dashboard.svg" alt="img" /><span>
                  Dashboard</span>
              </a>
            </li>
            <li id="productButton" class="submenu">
              <a href="javascript:void(0);"><img src="<?php echo APP_URL; ?>/admin/assets/img/icons/product.svg" alt="img" /><span>
                  Product</span>
                <span class="menu-arrow"></span></a>
              <ul>
                <li><a id="productListLink" class="text-linear-hover" href="<?php echo APP_URL; ?>/admin/products/">Product List</a></li>
                <li><a id="categoryListLink" class="text-linear-hover" href="<?php echo APP_URL; ?>/admin/categories/">Category List</a></li>
              </ul>
            </li>
            <li id="salesButton" class="submenu">
              <a href="javascript:void(0);"><img src="<?php echo APP_URL; ?>/admin/assets/img/icons/sales1.svg" alt="img" /><span>
                  Sales</span>
                <span class="menu-arrow"></span></a>
              <ul>
                <li><a id="orderListLink" class="text-linear-hover" href="<?php echo APP_URL; ?>/admin/orders/">Order List</a></li>
              </ul>
            </li>
            <li id="peopleButton" class="submenu">
              <a href="javascript:void(0);"><img src="<?php echo APP_URL; ?>/admin/assets/img/icons/users1.svg" alt="img" /><span>
                  People</span>
                <span class="menu-arrow"></span></a>
              <ul>
                <li><a id="customerListLink" class="text-linear-hover" href="<?php echo APP_URL; ?>/admin/customers/">Customer List</a></li>
                <li><a id="userListLink" class="text-linear-hover" href="<?php echo APP_URL; ?>/admin/users/">User List</a></li>
              </ul>
            </li>
          </ul>
        </div>
      </div>
    </div>