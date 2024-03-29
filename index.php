<?php require_once "inc/components/header.php"; ?>
<?php $conn = require_once "inc/db.php";
$newProducts = Product::getAllProducts($conn, 20);

// Query Laptops
$laptopParams =
    [
        'filters' => ['categoryId' => 2],
        'limit' => 20,
    ];

$laptops = Product::getProductsByCategory($conn, $laptopParams)['data'];
// End Query Laptops

// Query PCs
$computerParams =
    [
        'filters' => ['categoryId' => 6],
        'limit' => 20,
    ];

$computers = Product::getProductsByCategory($conn, $computerParams)['data'];
// End Query PCs

// Query Monitors
$monitorParams =
    [
        'filters' => ['categoryId' => 7],
        'limit' => 20,
    ];

$monitors = Product::getProductsByCategory($conn, $monitorParams)['data'];
// End Query Monitors
?>
<div id="main-content" class="main-content">
    <div id="promotion-slider">
        <div class="container">
            <div class="row">
                <div class="single-promotion position-relative">
                    <div class="promotion-container">
                        <img class="promotion-img object-fit-fill" src="assets/img/promotion.png" alt="promotion" />
                    </div>
                    <div class="promotion-container">
                        <img class="promotion-img object-fit-fill" src="assets/img/slider_1.jpg" alt="promotion" />
                    </div>
                    <div class="promotion-container">
                        <img class="promotion-img object-fit-fill" src="assets/img/slider_2.jpg" alt="promotion" />
                    </div>
                    <div class="promotion-container">
                        <img class="promotion-img object-fit-fill" src="assets/img/promotion_3.jpg" alt="promotion" />
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="product-slider">
        <div class="container">
            <div class="row">
                <div class="product-slider__desc d-flex justify-content-between align-items-baseline">
                    <h2 class="product-slider__desc__title mt-3 mb-1">New Products</h2>
                    <a class="product-slider__desc__link" href="#">See All New Products</a>
                </div>
                <div class="multiple-product-slider">
                    <?php foreach ($newProducts as $newProduct) : ?>
                        <div class="product-card-container">
                            <div class="card-product-detail" data-index="<?php echo $newProduct->id; ?>">
                                <div class="item-status d-flex">
                                    <?php if ($newProduct->stockQuantity > 0) : ?>
                                        <img src="assets/img/stock.svg" alt="status-product" />
                                        <span class="true">&nbsp;in stock</span>
                                    <?php else : ?>
                                        <img src="assets/img/call.svg" alt="status-product" />
                                        <span class="false">&nbsp;check availability</span>
                                    <?php endif; ?>
                                </div>
                                <div class="image-container">
                                    <img class="object-fit-contain" src="<?php echo $newProduct->imageUrl; ?>" alt="cpu" />
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
                                        <?php echo $newProduct->name; ?>
                                    </p>
                                </div>
                                <div class="price-container d-flex flex-column align-items-start">
                                    <span class="old-price">$<?php echo $newProduct->price; ?></span>
                                    <span class="new-price">$<?php echo $newProduct->price; ?></span>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>

    <div id="add-on">
        <div class="container">
            <div class="row">
                <div class="advertisement d-flex justify-content-center align-items-center gap-3">
                    <img src="assets/img/zip.svg" alt="zip" />
                    <img src="assets/img/vector.svg" alt="vector" />
                    <p class="m-0">own it now, up to 6 months interest free learn more</p>
                </div>
            </div>
        </div>
    </div>

    <!-- <div id="outstanding-product">
        <div class="container">
            <div class="multiple-product-outstanding">
                <div class="row">
                    <div class="mb-2 col col-sm-6 col-md-4 col-lg-3 col-xl-2-4">
                        <div class="custom-build d-flex flex-column justify-content-center align-items-center">
                            <h3 class="m-auto text-center">Custom<br />Builds</h3>
                            <a class="mb-4" href="#">See All Products</a>
                        </div>
                    </div>
                    <div class="mb-2 col col-sm-6 col-md-4 col-lg-3 col-xl-2-4">
                        <div class="card-product-detail">
                            <div class="item-status d-flex">
                                <img src="assets/img/stock.svg" alt="in-stock" />
                                <span class="true">&nbsp;in stock</span>
                            </div>
                            <div class="image-container">
                                <img class="object-fit-contain" src="assets/img/cpu_1.png" alt="cpu" />
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
                                    EX DISPLAY : MSI Pro 16 Flex-036AU 15.6 MULTITOUCH All-In-On...
                                </p>
                            </div>
                            <div class="price-container d-flex flex-column align-items-start">
                                <span class="old-price">$499.00</span>
                                <span class="new-price">$499.00</span>
                            </div>
                        </div>
                    </div>
                    <div class="mb-2 col col-sm-6 col-md-4 col-lg-3 col-xl-2-4">
                        <div class="card-product-detail">
                            <div class="item-status d-flex">
                                <img src="assets/img/stock.svg" alt="in-stock" />
                                <span class="true">&nbsp;in stock</span>
                            </div>
                            <div class="image-container">
                                <img class="object-fit-contain" src="assets/img/cpu_1.png" alt="cpu" />
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
                                    EX DISPLAY : MSI Pro 16 Flex-036AU 15.6 MULTITOUCH All-In-On...
                                </p>
                            </div>
                            <div class="price-container d-flex flex-column align-items-start">
                                <span class="old-price">$499.00</span>
                                <span class="new-price">$499.00</span>
                            </div>
                        </div>
                    </div>
                    <div class="mb-2 col col-sm-6 col-md-4 col-lg-3 col-xl-2-4">
                        <div class="card-product-detail">
                            <div class="item-status d-flex">
                                <img src="assets/img/stock.svg" alt="in-stock" />
                                <span class="true">&nbsp;in stock</span>
                            </div>
                            <div class="image-container">
                                <img class="object-fit-contain" src="assets/img/cpu_1.png" alt="cpu" />
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
                                    EX DISPLAY : MSI Pro 16 Flex-036AU 15.6 MULTITOUCH All-In-On...
                                </p>
                            </div>
                            <div class="price-container d-flex flex-column align-items-start">
                                <span class="old-price">$499.00</span>
                                <span class="new-price">$499.00</span>
                            </div>
                        </div>
                    </div>
                    <div class="mb-2 col col-sm-6 col-md-4 col-lg-3 col-xl-2-4">
                        <div class="card-product-detail">
                            <div class="item-status d-flex">
                                <img src="assets/img/stock.svg" alt="in-stock" />
                                <span class="true">&nbsp;in stock</span>
                            </div>
                            <div class="image-container">
                                <img class="object-fit-contain" src="assets/img/custom-build_4.jpg" alt="cpu" />
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
                                    EX DISPLAY : MSI Pro 16 Flex-036AU 15.6 MULTITOUCH All-In-On...
                                </p>
                            </div>
                            <div class="price-container d-flex flex-column align-items-start">
                                <span class="old-price">$499.00</span>
                                <span class="new-price">$499.00</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div> -->

    <div id="product-slider">
        <div class="container">
            <div class="row">
                <div class="product-slider__desc d-flex justify-content-between align-items-baseline">
                    <h2 class="product-slider__desc__title mt-3 mb-1">Laptop</h2>
                    <a class="product-slider__desc__link" href="#">See All Laptops</a>
                </div>
                <div class="multiple-product-slider">
                    <?php foreach ($laptops as $laptop) : ?>
                        <div class="product-card-container">
                            <div class="card-product-detail" data-index="<?php echo $laptop->id; ?>">
                                <div class="item-status d-flex">
                                    <?php if ($laptop->stockQuantity > 0) : ?>
                                        <img src="assets/img/stock.svg" alt="status-product" />
                                        <span class="true">&nbsp;in stock</span>
                                    <?php else : ?>
                                        <img src="assets/img/call.svg" alt="status-product" />
                                        <span class="false">&nbsp;check availability</span>
                                    <?php endif; ?>
                                </div>
                                <div class="image-container">
                                    <img class="object-fit-contain" src="<?php echo $laptop->imageUrl; ?>" alt="cpu" />
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
                                        <?php echo $laptop->name; ?>
                                    </p>
                                </div>
                                <div class="price-container d-flex flex-column align-items-start">
                                    <span class="old-price">$<?php echo $laptop->price; ?></span>
                                    <span class="new-price">$<?php echo $laptop->price; ?></span>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>

    <div id="product-slider">
        <div class="container">
            <div class="row">
                <div class="product-slider__desc d-flex justify-content-between align-items-baseline">
                    <h2 class="product-slider__desc__title mt-3 mb-1">Desktop</h2>
                    <a class="product-slider__desc__link" href="#">See All Desktops</a>
                </div>
                <div class="multiple-product-slider">
                    <?php foreach ($computers as $computer) : ?>
                        <div class="product-card-container">
                            <div class="card-product-detail" data-index="<?php echo $computer->id; ?>">
                                <div class="item-status d-flex">
                                    <?php if ($computer->stockQuantity > 0) : ?>
                                        <img src="assets/img/stock.svg" alt="status-product" />
                                        <span class="true">&nbsp;in stock</span>
                                    <?php else : ?>
                                        <img src="assets/img/call.svg" alt="status-product" />
                                        <span class="false">&nbsp;check availability</span>
                                    <?php endif; ?>
                                </div>
                                <div class="image-container">
                                    <img class="object-fit-contain" src="<?php echo $computer->imageUrl; ?>" alt="cpu" />
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
                                        <?php echo $computer->name; ?>
                                    </p>
                                </div>
                                <div class="price-container d-flex flex-column align-items-start">
                                    <span class="old-price">$<?php echo $computer->price; ?></span>
                                    <span class="new-price">$<?php echo $computer->price; ?></span>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>

    <div id="product-slider">
        <div class="container">
            <div class="row">
                <div class="product-slider__desc d-flex justify-content-between align-items-baseline">
                    <h2 class="product-slider__desc__title mt-3 mb-1">Monitor</h2>
                    <a class="product-slider__desc__link" href="#">See All Monitors</a>
                </div>
                <div class="multiple-product-slider">
                    <?php foreach ($monitors as $monitor) : ?>
                        <div class="product-card-container">
                            <div class="card-product-detail" data-index="<?php echo $monitor->id; ?>">
                                <div class="item-status d-flex">
                                    <?php if ($monitor->stockQuantity > 0) : ?>
                                        <img src="assets/img/stock.svg" alt="status-product" />
                                        <span class="true">&nbsp;in stock</span>
                                    <?php else : ?>
                                        <img src="assets/img/call.svg" alt="status-product" />
                                        <span class="false">&nbsp;check availability</span>
                                    <?php endif; ?>
                                </div>
                                <div class="image-container">
                                    <img class="object-fit-contain" src="<?php echo $monitor->imageUrl; ?>" alt="cpu" />
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
                                        <?php echo $monitor->name; ?>
                                    </p>
                                </div>
                                <div class="price-container d-flex flex-column align-items-start">
                                    <span class="old-price">$<?php echo $monitor->price; ?></span>
                                    <span class="new-price">$<?php echo $monitor->price; ?></span>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>

    <div id="brand">
        <div class="container">
            <div class="d-flex justify-content-between overflow-hidden">
                <a href="#" class="d-inline-flex">
                    <img src="<?php echo APP_URL; ?>/assets/img/brand_1.png" alt="brand" class="item-brand object-fit-contain" />
                </a>

                <a href="#" class="d-inline-flex">
                    <img src="<?php echo APP_URL; ?>/assets/img/brand_2.png" alt="brand" class="item-brand object-fit-contain" />
                </a>

                <a href="#" class="d-inline-flex">
                    <img src="<?php echo APP_URL; ?>/assets/img/brand_3.png" alt="brand" class="item-brand object-fit-contain" />
                </a>

                <a href="#" class="d-inline-flex">
                    <img src="<?php echo APP_URL; ?>/assets/img/brand_4.png" alt="brand" class="item-brand object-fit-contain" />
                </a>

                <a href="#" class="d-inline-flex">
                    <img src="<?php echo APP_URL; ?>/assets/img/brand_5.png" alt="brand" class="item-brand object-fit-contain" />
                </a>

                <a href="#" class="d-inline-flex">
                    <img src="<?php echo APP_URL; ?>/assets/img/brand_6.png" alt="brand" class="item-brand object-fit-contain" />
                </a>

                <a href="#" class="d-inline-flex">
                    <img src="<?php echo APP_URL; ?>/assets/img/brand_7.png" alt="brand" class="item-brand object-fit-contain" />
                </a>
            </div>
        </div>
    </div>
</div>
<?php require_once "inc/components/footer.php"; ?>
<script src="<?php echo APP_URL; ?>/js/header/dropdown.js"></script>
<script src="<?php echo APP_URL; ?>/js/header/searchbar.js"></script>
<script src="<?php echo APP_URL; ?>/js/body/promotion.js"></script>
<script src="<?php echo APP_URL; ?>/js/body/product.js"></script>
<script>
    const productCards = document.querySelectorAll('.card-product-detail');
    console.log(productCards)
    productCards.forEach(productCard => {
        productCard.addEventListener('click', () => {
            window.location.href = `<?php echo APP_URL; ?>/product/product-detail.php?product_id=${productCard.dataset.index}`;
        })
    })
</script>