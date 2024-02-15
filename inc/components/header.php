<?php
require_once dirname(__DIR__) . "/init.php";
define("APP_URL", "http://localhost/se-php");
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
    <link rel="stylesheet" href="<?php echo APP_URL; ?>/assets/fontawesome/css/all.min.css" />
    <script src="<?php echo APP_URL; ?>/assets/bootstrap/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" type="text/css" href="<?php echo APP_URL; ?>/assets/slick/slick.css" />
    <!-- Add the new slick-theme.css if you want the default styling -->
    <link rel="stylesheet" type="text/css" href="<?php echo APP_URL; ?>/assets/slick/slick-theme.css" />
    <script type="text/javascript" src="<?php echo APP_URL; ?>/assets/jquery/jquery.min.js"></script>
    <script type="text/javascript" src="<?php echo APP_URL; ?>/assets/jquery/jquery-migrate.min.js"></script>
    <script type="text/javascript" src="<?php echo APP_URL; ?>/assets/slick/slick.min.js"></script>
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
                            <div class="top-right-header d-flex justify-content-end">
                                <div class="top-right-header__header-contact header-contact">
                                    <div class="header-contact__phone-number">
                                        <a class="phone-number text-decoration-none" href="tel:+(00) 1234 5678">Call Us: (00) 1234 5678</a>
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
                    <div class="col-sm-2 col-lg-5 col-xl-7" id="main-header-menu">
                        <div class="main-header__menu d-flex align-items-center justify-content-between">
                            <nav class="navbar navbar-expand-lg navbar-light bg-transparent w-100">
                                <div class="container-fluid p-0">
                                    <a class="navbar-brand d-sm-none d-xl-block" href="#">
                                        <img src="<?php echo APP_URL; ?>/assets/img/logo.svg" alt="logo" />
                                    </a>
                                    <button class="navbar-toggler mb-3" id="button-header-collapse" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                                        <span class="navbar-toggler-icon"></span>
                                    </button>
                                    <div class="collapse navbar-collapse" id="navbarSupportedContent">
                                        <ul class="navbar-nav d-flex w-100 justify-content-between mb-2 mb-lg-0">
                                            <li class="nav-item dropdown">
                                                <a class="nav-link nav-dropdown-toggle dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                    Laptops
                                                </a>
                                                <section class="dropdown-menu dropdown-menu-mega" aria-labelledby="navbarDropdown">
                                                    <div class="grid-container">
                                                        <div class="list-item">
                                                            <ul class="list-item__detail list-unstyled text-start">
                                                                <li>Everyday Use Notebooks</li>
                                                                <li>MSI Workstation Series</li>
                                                                <li>MSI Prestige Series</li>
                                                                <li>Gaming Notebooks</li>
                                                                <li>Tablets And Pads</li>
                                                                <li>Netbooks</li>
                                                                <li>Infinity Gaming Notebooks</li>
                                                            </ul>
                                                        </div>
                                                        <div class="list-item-detail">
                                                            <ul id="main-header__list-item-detail" class="list-item-detail__content list-unstyled d-flex flex-row gap-2">
                                                                <li>
                                                                    <div class="card-product-detail">
                                                                        <div class="item-status d-flex">
                                                                            <img src="<?php echo APP_URL; ?>/assets/img/stock.svg" alt="in-stock" />
                                                                            <span class="true">&nbsp;in stock</span>
                                                                        </div>
                                                                        <div class="image-container">
                                                                            <img class="object-fit-contain" src="<?php echo APP_URL; ?>/assets/img/laptop_2.png" alt="cpu" />
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
                                                                                EX DISPLAY : MSI Pro 16 Flex-036AU
                                                                                15.6 MULTITOUCH All-In-On...
                                                                            </p>
                                                                        </div>
                                                                        <div class="price-container d-flex flex-column align-items-start">
                                                                            <span class="old-price">$499.00</span>
                                                                            <span class="new-price">$499.00</span>
                                                                        </div>
                                                                    </div>
                                                                </li>
                                                                <li>
                                                                    <div class="card-product-detail">
                                                                        <div class="item-status d-flex">
                                                                            <img src="<?php echo APP_URL; ?>/assets/img/stock.svg" alt="in-stock" />
                                                                            <span class="true">&nbsp;in stock</span>
                                                                        </div>
                                                                        <div class="image-container">
                                                                            <img class="object-fit-contain" src="<?php echo APP_URL; ?>/assets/img/cpu_1.png" alt="cpu" />
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
                                                                                EX DISPLAY : MSI Pro 16 Flex-036AU
                                                                                15.6 MULTITOUCH All-In-On...
                                                                            </p>
                                                                        </div>
                                                                        <div class="price-container d-flex flex-column align-items-start">
                                                                            <span class="old-price">$499.00</span>
                                                                            <span class="new-price">$499.00</span>
                                                                        </div>
                                                                    </div>
                                                                </li>
                                                                <li>
                                                                    <div class="card-product-detail">
                                                                        <div class="item-status d-flex">
                                                                            <img src="<?php echo APP_URL; ?>/assets/img/stock.svg" alt="in-stock" />
                                                                            <span class="true">&nbsp;in stock</span>
                                                                        </div>
                                                                        <div class="image-container">
                                                                            <img class="object-fit-contain" src="<?php echo APP_URL; ?>/assets/img/custom-build_1.jpg" alt="cpu" />
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
                                                                                EX DISPLAY : MSI Pro 16 Flex-036AU
                                                                                15.6 MULTITOUCH All-In-On...
                                                                            </p>
                                                                        </div>
                                                                        <div class="price-container d-flex flex-column align-items-start">
                                                                            <span class="old-price">$499.00</span>
                                                                            <span class="new-price">$499.00</span>
                                                                        </div>
                                                                    </div>
                                                                </li>
                                                                <li>
                                                                    <div class="card-product-detail">
                                                                        <div class="item-status d-flex">
                                                                            <img src="<?php echo APP_URL; ?>/assets/img/stock.svg" alt="in-stock" />
                                                                            <span class="true">&nbsp;in stock</span>
                                                                        </div>
                                                                        <div class="image-container">
                                                                            <img class="object-fit-contain" src="<?php echo APP_URL; ?>/assets/img/desk_3.png" alt="cpu" />
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
                                                                                EX DISPLAY : MSI Pro 16 Flex-036AU
                                                                                15.6 MULTITOUCH All-In-On...
                                                                            </p>
                                                                        </div>
                                                                        <div class="price-container d-flex flex-column align-items-start">
                                                                            <span class="old-price">$499.00</span>
                                                                            <span class="new-price">$499.00</span>
                                                                        </div>
                                                                    </div>
                                                                </li>
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
                                                <a class="nav-link active" aria-current="page" href="#">Desktop PCs</a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link" href="#">Networking Devices</a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link" href="#">Printers & Scanners</a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link" href="#">All Other Products</a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </nav>
                        </div>
                    </div>
                    <div class="col" id="main-header-mechanism">
                        <div class="main-header__mechanism h-100 d-flex align-items-center justify-content-end">
                            <div class="search-bar-container flex-grow-1">
                                <form action="" class="search" id="search-bar">
                                    <input type="text" placeholder="Type something..." name="search" class="search__input" />

                                    <div class="search__button" id="search-button">
                                        <i class="fa-solid fa-magnifying-glass search__icon"></i>
                                        <i class="fa-solid fa-xmark search__close"></i>
                                    </div>
                                </form>
                            </div>

                            <ul class="main-header__mechanism--button d-flex list-unstyled m-0">
                                <li class="cart-component-dropdown">
                                    <div class="dropdown dropdown-cart">
                                        <button class="btn dropdown-toggle dropdown-cart-btn" id="dropdown-cart-btn" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                            <img src="<?php echo APP_URL; ?>/assets/img/cart-btn.svg" alt="cart-btn" />
                                            <span class="dropdown-cart-btn__count"> 0 </span>
                                        </button>
                                        <ul class="dropdown-menu" id="dropdown-menu-cart">
                                            <li class="cart-intro-container">
                                                <div class="cart-intro d-flex flex-column justify-content-center align-items-center">
                                                    <h6 class="cart-intro__cart-title">My Cart</h6>
                                                    <p class="cart_intro__cart-info">2 items in cart</p>
                                                    <button class="cart-intro__cart-view-btn">
                                                        <span class="cart-intro__cart-view-btn__title">View or Edit Your Cart</span>
                                                    </button>
                                                </div>
                                            </li>
                                            <!-- Cart Product -->
                                            <li class="cart-product-container">
                                                <div class="cart-product d-flex justify-content-center align-items-center">
                                                    <div class="cart-product__image d-flex">
                                                        <div class="cart-product-quantity-container d-flex align-items-center">
                                                            <span class="cart-product__quantity">1</span>
                                                            <span class="cart-product__sign">x</span>
                                                        </div>
                                                        <img src="<?php echo APP_URL; ?>/assets/img/cpu.png" alt="cart-product" class="cart-product__img object-fit-contain" />
                                                    </div>
                                                    <p class="cart-product__desc m-0">
                                                        EX DISPLAY : MSI Pro 16 Flex-036AU 15.6 MULTITOUCH
                                                        All-In-On...
                                                    </p>
                                                </div>
                                            </li>
                                            <li class="cart-product-container">
                                                <div class="cart-product d-flex justify-content-center align-items-center">
                                                    <div class="cart-product__image d-flex">
                                                        <div class="cart-product-quantity-container d-flex align-items-center">
                                                            <span class="cart-product__quantity">1</span>
                                                            <span class="cart-product__sign">x</span>
                                                        </div>
                                                        <img src="<?php echo APP_URL; ?>/assets/img/cpu.png" alt="cart-product" class="cart-product__img object-fit-contain" />
                                                    </div>
                                                    <p class="cart-product__desc m-0">
                                                        EX DISPLAY : MSI Pro 16 Flex-036AU 15.6 MULTITOUCH
                                                        All-In-On...
                                                    </p>
                                                </div>
                                            </li>
                                            <!-- End Cart Product -->
                                            <li class="cart-payment-container">
                                                <div class="cart-payment d-flex gap-2 flex-column justify-content-center align-items-center">
                                                    <div class="payment__subtotal">
                                                        <span class="payment__subtotal__title">Subtotal:</span>
                                                        <span class="payment__subtotal__number">$499.00</span>
                                                    </div>
                                                    <div class="payment__btn">
                                                        <button class="payment__btn__checkout">
                                                            <span class="payment__btn__checkout__title">
                                                                Go to Checkout
                                                            </span>
                                                        </button>
                                                    </div>
                                                </div>
                                            </li>
                                        </ul>
                                    </div>
                                </li>
                                <li class="user-component-dropdown">
                                    <div class="dropdown dropdown-user">
                                        <button class="btn dropdown-toggle dropdown-user-btn" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="fa-sharp fa-solid fa-user-secret"></i>
                                        </button>
                                        <ul class="dropdown-menu" id="dropdown-menu-user">
                                            <li>
                                                <a class="dropdown-item" href="#">My Account</a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item" href="#">Orders</a>
                                            </li>
                                            <li>
                                                <hr class="dropdown-divider" />
                                            </li>
                                            <li>
                                                <a class="dropdown-item" href="#">Log out</a>
                                            </li>
                                        </ul>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>
    <!-- End Header -->