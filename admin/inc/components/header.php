<?php
require_once  dirname(dirname(dirname(__DIR__))) . "/inc/init.php";
?>

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

  <link rel="shortcut icon" type="image/x-icon" href="assets/img/favicon.jpg" />
  <link rel="stylesheet" href="assets/css/bootstrap.min.css" />
  <link rel="stylesheet" href="assets/css/animate.css" />
  <link rel="stylesheet" href="assets/css/dataTables.bootstrap4.min.css" />
  <link rel="stylesheet" href="assets/plugins/fontawesome/css/fontawesome.min.css" />
  <link rel="stylesheet" href="assets/plugins/fontawesome/css/all.min.css" />
  <link rel="stylesheet" href="assets/css/style.css" />
</head>

<body>
  <!-- <div id="global-loader">
    <div class="whirly-loader"></div>
  </div> -->

  <div class="main-wrapper">
    <div class="header">
      <div class="header-left active">
        <a href="index.html" class="logo">
          <img src="assets/img/logo.png" alt="" />
        </a>
        <a href="index.html" class="logo-small">
          <img src="assets/img/logo-small.png" alt="" />
        </a>
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
        <li class="nav-item">
          <div class="top-nav-search">
            <a href="javascript:void(0);" class="responsive-search">
              <i class="fa fa-search"></i>
            </a>
            <form action="#">
              <div class="searchinputs">
                <input type="text" placeholder="Search Here ..." />
                <div class="search-addon">
                  <span><img src="assets/img/icons/closes.svg" alt="img" /></span>
                </div>
              </div>
              <a class="btn" id="searchdiv"><img src="assets/img/icons/search.svg" alt="img" /></a>
            </form>
          </div>
        </li>

        <li class="nav-item dropdown has-arrow flag-nav">
          <a class="nav-link dropdown-toggle" data-bs-toggle="dropdown" href="javascript:void(0);" role="button">
            <img src="assets/img/flags/us1.png" alt="" height="20" />
          </a>
          <div class="dropdown-menu dropdown-menu-right">
            <a href="javascript:void(0);" class="dropdown-item">
              <img src="assets/img/flags/us.png" alt="" height="16" /> English
            </a>
            <a href="javascript:void(0);" class="dropdown-item">
              <img src="assets/img/flags/fr.png" alt="" height="16" /> French
            </a>
            <a href="javascript:void(0);" class="dropdown-item">
              <img src="assets/img/flags/es.png" alt="" height="16" /> Spanish
            </a>
            <a href="javascript:void(0);" class="dropdown-item">
              <img src="assets/img/flags/de.png" alt="" height="16" /> German
            </a>
          </div>
        </li>

        <li class="nav-item dropdown has-arrow main-drop">
          <a href="javascript:void(0);" class="dropdown-toggle nav-link userset" data-bs-toggle="dropdown">
            <span class="user-img"><img src="assets/img/profiles/avator1.jpg" alt="" />
              <span class="status online"></span></span>
          </a>
          <div class="dropdown-menu menu-drop-user">
            <div class="profilename">
              <div class="profileset">
                <span class="user-img"><img src="assets/img/profiles/avator1.jpg" alt="" />
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
              <a class="dropdown-item logout pb-0" href="signin.html"><img src="assets/img/icons/log-out.svg" class="me-2" alt="img" />Logout</a>
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
            <li class="active">
              <a href="./"><img src="assets/img/icons/dashboard.svg" alt="img" /><span>
                  Dashboard</span>
              </a>
            </li>
            <li class="submenu">
              <a href="javascript:void(0);"><img src="assets/img/icons/product.svg" alt="img" /><span>
                  Product</span>
                <span class="menu-arrow"></span></a>
              <ul>
                <li><a href="products.html">Product List</a></li>
                <li><a href="add-product.html">Add Product</a></li>
                <li><a href="categories.html">Category List</a></li>
                <li><a href="add-category.html">Add Category</a></li>
              </ul>
            </li>
            <li class="submenu">
              <a href="javascript:void(0);"><img src="assets/img/icons/sales1.svg" alt="img" /><span>
                  Sales</span>
                <span class="menu-arrow"></span></a>
              <ul>
                <li><a href="orders.html">Order List</a></li>
              </ul>
            </li>
            <li class="submenu">
              <a href="javascript:void(0);"><img src="assets/img/icons/users1.svg" alt="img" /><span>
                  People</span>
                <span class="menu-arrow"></span></a>
              <ul>
                <li><a href="customers.html">Customer List</a></li>
                <li><a href="add-customer.html">Add Customer</a></li>
                <li><a href="users.html">User List</a></li>
                <li><a href="add-user.html">Add User</a></li>
              </ul>
            </li>
            <li class="submenu">
              <a href="javascript:void(0);"><i data-feather="alert-octagon"></i>
                <span> Error Pages </span> <span class="menu-arrow"></span></a>
              <ul>
                <li><a href="error-404.html">404 Error </a></li>
                <li><a href="error-500.html">500 Error </a></li>
              </ul>
            </li>
          </ul>
        </div>
      </div>
    </div>
  </div>