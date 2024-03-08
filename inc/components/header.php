<?php
require_once dirname(__DIR__) . "/init.php";
?>

<?php
// set default value for cart quantity
$cartQuantity = 0;
if (Auth::isLoggedIn()) {
    $conn = require_once dirname(__DIR__) . '/db.php';
    $cartDetail = Cart::getCartDetailByUserId($conn, $_SESSION['userId']);
    $cartQuantity = count($cartDetail['data']);
}

if (!isset($conn))
    $conn = require_once dirname(__DIR__) . '/db.php';
$outstandingProducts = Product::getAllProducts($conn, 4, 120);

// print_r($outstandingProducts);
?>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Document</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@100;200;300;400;500;600;700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="<?php echo APP_URL; ?>/assets/css/main.css" />
    <link rel="stylesheet" href="<?php echo APP_URL; ?>/assets/bootstrap/css/bootstrap.css" />
    <script type="text/javascript" src="<?php echo APP_URL; ?>/assets/jquery/jquery.min.js"></script>
    <script type="text/javascript" src="<?php echo APP_URL; ?>/assets/jquery/jquery-migrate.min.js"></script>
    <script type="text/javascript" src="<?php echo APP_URL; ?>/assets/jquery/jquery.validate.min.js"></script>
    <script type="text/javascript" src="<?php echo APP_URL; ?>/assets/jquery/additional-methods.min.js"></script>
    <script type="text/javascript" src="<?php echo APP_URL; ?>/assets/bootstrap/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="<?php echo APP_URL; ?>/assets/fontawesome/css/all.min.css" />
    <link rel="stylesheet" type="text/css" href="<?php echo APP_URL; ?>/assets/slick/slick.css" />
    <!-- Add the new slick-theme.css if you want the default styling -->
    <link rel="stylesheet" type="text/css" href="<?php echo APP_URL; ?>/assets/slick/slick-theme.css" />
    <script type="text/javascript" src="<?php echo APP_URL; ?>/assets/slick/slick.min.js"></script>
    <script type="text/javascript" src="<?php echo APP_URL; ?>/assets/jquery/popper.min.js"></script>

    <link href="<?php echo APP_URL; ?>/assets/toastr/toastr.min.css" rel="stylesheet" />
    <script src="<?php echo APP_URL; ?>/assets/toastr/toastr.min.js"></script>
    <script src="<?php echo APP_URL; ?>/assets/toastr/toastr.config.js"></script>
    <script src="https://www.paypal.com/sdk/js?client-id=<?php echo PAYPAL_SANDBOX ? PAYPAL_SANDBOX_CLIENT_ID : PAYPAL_PROD_CLIENT_ID; ?>&currency=<?php echo CURRENCY; ?>"></script>
</head>

<body>
    <header id="header">
        <div class="top-header">
            <div class="container">
                <div class="row">
                    <div class="top-header__content">
                        <div class="col-xl-3 col-md-6 col-sm-6">
                            <div class="top-left-header">
                                <div class="top-left-header__active-time active-time">
                                    <p class="active-time__date m-0">Mon-Thu:&nbsp;&nbsp;</p>
                                    <p class="active-time__hours m-0">9:00 AM - 5:30 PM</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-6 d-none d-xl-block">
                            <div class="top-middle-header d-flex justify-content-evenly">
                                <p class="top-header-description m-0">
                                    Visit our showroom in 1234 Street Adress City Address, 1234&nbsp;
                                    <a href="#" class="text-decoration-underline">Contact Us</a>
                                </p>
                            </div>
                        </div>
                        <div class="col-xl-3 col-md-6 col-sm-6">
                            <div class="top-right-header d-flex justify-content-end align-items-center">
                                <div class="top-right-header__header-contact header-contact">
                                    <div class="header-contact__phone-number d-flex align-items-center">
                                        <a class="phone-number text-decoration-none d-inline-block" href="tel:+(00) 1234 5678">Call Us: (00) 1234 5678</a>
                                    </div>
                                    <div class="header-contact__social-media">
                                        <ul class="social-media-list list-unstyled m-0">
                                            <li>
                                                <a href="#">
                                                    <img src="<?php echo APP_URL; ?>/assets/img/ant-design_facebook-filled.svg" alt="facebook" />
                                                </a>
                                            </li>
                                            <li>
                                                <a href="#">
                                                    <img src="<?php echo APP_URL; ?>/assets/img/ant-design_instagram-filled.svg" alt="instagram" />
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="main-header">
            <div class="container">
                <div class="row">
                    <div class="col-sm-2 col-lg-5 col-xl-6" id="main-header-menu">
                        <div class="main-header__menu d-flex align-items-center justify-content-between">
                            <nav class="navbar navbar-header navbar-expand-lg navbar-light bg-white w-100">
                                <div class="container-fluid p-0">
                                    <a class="navbar-brand d-sm-none d-xl-block" href="<?php echo APP_URL; ?>">
                                        <img src="<?php echo APP_URL; ?>/assets/img/logo.svg" alt="logo" />
                                    </a>
                                    <button class="navbar-toggler mb-3" id="button-header-collapse" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                                        <span class="navbar-toggler-icon"></span>
                                    </button>
                                    <div class="collapse navbar-collapse" id="navbarSupportedContent">
                                        <ul class="navbar-nav d-flex w-100 justify-content-between mb-2 mb-lg-0">
                                            <li class="nav-item dropdown">
                                                <a class="nav-link nav-dropdown-toggle dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                    Categories
                                                </a>
                                                <section class="dropdown-menu dropdown-menu-mega" aria-labelledby="navbarDropdown">
                                                    <div class="grid-container">
                                                        <div class="list-item">
                                                            <ul class="list-item__detail list-unstyled text-start">
                                                                <li>
                                                                    <a class="bg-transparent text-black text-decoration-none" href="<?php echo APP_URL; ?>/product/?categoryId=1">Smartphone</a>
                                                                </li>
                                                                <li>
                                                                    <a class="bg-transparent text-black text-decoration-none" href="<?php echo APP_URL; ?>/product/?categoryId=2">Laptop</a>
                                                                </li>
                                                                <li>
                                                                    <a class="bg-transparent text-black text-decoration-none" href="<?php echo APP_URL; ?>/product/?categoryId=3">Accessories</a>
                                                                </li>
                                                                <li>
                                                                    <a class="bg-transparent text-black text-decoration-none" href="<?php echo APP_URL; ?>/product/?categoryId=4">Studio</a>
                                                                </li>
                                                                <li>
                                                                    <a class="bg-transparent text-black text-decoration-none" href="<?php echo APP_URL; ?>/product/?categoryId=5">Camera</a>
                                                                </li>
                                                                <li>
                                                                    <a class="bg-transparent text-black text-decoration-none" href="<?php echo APP_URL; ?>/product/?categoryId=6">PC, Monitor</a>
                                                                </li>
                                                                <li>
                                                                    <a class="bg-transparent text-black text-decoration-none" href="<?php echo APP_URL; ?>/product/?categoryId=7">Television</a>
                                                                </li>
                                                            </ul>
                                                        </div>
                                                        <div class="list-item-detail">
                                                            <ul id="main-header__list-item-detail" class="list-item-detail__content list-unstyled d-flex flex-row gap-2">
                                                                <?php foreach ($outstandingProducts as $product) : ?>
                                                                    <li>
                                                                        <div class="card-product-detail">
                                                                            <div class="item-status d-flex">
                                                                                <?php if ($product->stockQuantity > 0) : ?>
                                                                                    <img src="<?php echo APP_URL; ?>/assets/img/stock.svg" alt="status-product" />
                                                                                    <span class="true">&nbsp;in stock</span>
                                                                                <?php else : ?>
                                                                                    <img src="assets/img/call.svg" alt="status-product" />
                                                                                    <span class="false">&nbsp;check availability</span>
                                                                                <?php endif; ?>
                                                                            </div>
                                                                            <div class="image-container">
                                                                                <img class="object-fit-contain" src="<?php echo $product->imageUrl; ?>" alt="cpu" />
                                                                            </div>
                                                                            <div class="evaluation d-flex align-items-center">
                                                                                <div class="star-check d-flex">
                                                                                    <span class="fa fa-star checked"></span>
                                                                                    <span class="fa fa-star checked"></span>
                                                                                    <span class="fa fa-star checked"></span>
                                                                                    <span class="fa fa-star checked"></span>
                                                                                    <span class="fa fa-star"></span>
                                                                                </div>
                                                                                <div class="star-review">
                                                                                    <span>Reviews (4)</span>
                                                                                </div>
                                                                            </div>
                                                                            <div class="title-container">
                                                                                <p class="product-title">
                                                                                    <?php echo $product->name; ?>
                                                                                </p>
                                                                            </div>
                                                                            <div class="price-container d-flex flex-column align-items-start">
                                                                                <span class="old-price">$<?php echo $product->price; ?></span>
                                                                                <span class="new-price">$<?php echo $product->price; ?></span>
                                                                            </div>
                                                                        </div>
                                                                    </li>
                                                                <?php endforeach; ?>
                                                            </ul>
                                                        </div>
                                                        <div class="list-brand">
                                                            <ul class="m-0 list-unstyled d-flex justify-content-between align-items-center">
                                                                <li>
                                                                    <a href="#" class="link-brand">
                                                                        <img src="<?php echo APP_URL; ?>/assets/img/brand_1.png" class="item-brand object-fit-contain" alt="brand" />
                                                                    </a>
                                                                </li>
                                                                <li>
                                                                    <a href="#" class="link-brand">
                                                                        <img src="<?php echo APP_URL; ?>/assets/img/brand_2.png" class="item-brand object-fit-contain" alt="brand" />
                                                                    </a>
                                                                </li>
                                                                <li>
                                                                    <a href="#" class="link-brand">
                                                                        <img src="<?php echo APP_URL; ?>/assets/img/brand_3.png" class="item-brand object-fit-contain" alt="brand" />
                                                                    </a>
                                                                </li>
                                                                <li>
                                                                    <a href="#" class="link-brand">
                                                                        <img src="<?php echo APP_URL; ?>/assets/img/brand_4.png" class="item-brand object-fit-contain" alt="brand" />
                                                                    </a>
                                                                </li>
                                                                <li>
                                                                    <a href="#" class="link-brand">
                                                                        <img src="<?php echo APP_URL; ?>/assets/img/brand_5.png" class="item-brand object-fit-contain" alt="brand" />
                                                                    </a>
                                                                </li>
                                                                <li>
                                                                    <a href="#" class="link-brand">
                                                                        <img src="<?php echo APP_URL; ?>/assets/img/brand_6.png" class="item-brand object-fit-contain" alt="brand" />
                                                                    </a>
                                                                </li>
                                                                <li>
                                                                    <a href="#" class="link-brand">
                                                                        <img src="<?php echo APP_URL; ?>/assets/img/brand_7.png" class="item-brand object-fit-contain" alt="brand" />
                                                                    </a>
                                                                </li>
                                                            </ul>
                                                        </div>
                                                    </div>
                                                </section>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link active" aria-current="page" href="<?php echo APP_URL; ?>/product/?categoryId=1">Smartphone</a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link" href="<?php echo APP_URL; ?>/product/?categoryId=2">Laptop</a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link" href="<?php echo APP_URL; ?>/product/?categoryId=6">Desktop PCs</a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link" href="<?php echo APP_URL; ?>/product/?categoryId=7">Television</a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </nav>
                        </div>
                    </div>
                    <div class="col" id="main-header-mechanism">
                        <div class="main-header__mechanism h-100 d-flex gap-4 align-items-center justify-content-end">
                            <div class="search-bar-container flex-grow-1">
                                <form action="" id="form-search" method="GET" class="input-group d-flex">
                                    <input type="search" name="search" id="input-search" class="form-control" style="border-radius: 0.375rem;">
                                    <button type="submit" class="btn btn-primary" id="search-submit" style="position: absolute; right: 0; top: 0; z-index: 9999; bottom: 0;">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </form>
                                <div class="search-box d-none" id="search-box">
                                </div>
                                <div class="search-box-layout d-none" id="search-box-layout"></div>
                            </div>

                            <ul class="main-header__mechanism--button d-flex list-unstyled m-0 gap-4">
                                <li class="cart-component">
                                    <div class="cart-container d-flex flex-column">
                                        <button class="btn cart-btn" id="cart-btn" type="button">
                                            <img src="<?php echo APP_URL; ?>/assets/img/cart-btn.svg" alt="cart-btn" />
                                            <span class="cart-btn__count"> <?php echo $cartQuantity; ?></span>
                                        </button>
                                        <span class="header-control-desc">Cart</span>
                                    </div>
                                </li>
                                <li class="user-component-dropdown d-flex align-items-center">
                                    <div class="dropdown dropdown-user">
                                        <?php if (Auth::isLoggedIn()) : ?>
                                            <button class="btn dropdown-toggle dropdown-user-btn" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                <?php if (isset($_SESSION['image'])) : ?>
                                                    <img src="<?php echo $_SESSION['image']; ?>" alt="user" class="user__dropdown__image object-fit-contain" />
                                                <?php else : ?>
                                                    <i class="fa-sharp fa-solid fa-user-secret"></i>
                                                <?php endif; ?>
                                            </button>
                                            <ul class="dropdown-menu" id="dropdown-menu-user">
                                                <li>
                                                    <a class="dropdown-item" href="<?php echo APP_URL; ?>/user">My Account</a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item" href="<?php echo APP_URL; ?>/user/order.php">Orders</a>
                                                </li>
                                                <li>
                                                    <hr class="dropdown-divider" />
                                                </li>
                                                <li>
                                                    <button class="dropdown-item" id="logout-btn">Log out</button>
                                                </li>
                                            </ul>
                                        <?php else : ?>
                                            <a href="<?php echo APP_URL; ?>/auth/login-register.php" class="login-btn text-decoration-none">
                                                <i class="fa-regular fa-circle-user"></i>
                                                <span class="header-control-desc">Login</span>
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="logout-form">
            <!-- Button trigger modal -->
            <button type="button" class="btn d-none btn-logout-primary" data-bs-toggle="modal" data-bs-target="#exampleModal">
            </button>

            <!-- Modal -->
            <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h1 class="modal-title text-primary fs-5" id="exampleModalLabel">Logout Confirmation</h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body fs-6">
                            Are you sure want to logout?
                        </div>
                        <div class="modal-footer d-flex justify-content-between align-items-center flex-nowrap">
                            <button type="button" class="btn btn-secondary btn-logout-control btn-logout-cancel" data-bs-dismiss="modal">Cancel</button>
                            <button type="button" class="btn btn-primary btn-logout-control btn-logout-confirm">Yes</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>
    <!-- End Header -->