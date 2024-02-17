<?php require_once "../inc/components/header.php"; ?>
<?php require_once "../inc/utils.php" ?>
<?php
if ($_SERVER["REQUEST_METHOD"] !== "GET" || !isset($_GET["product_id"])) {
    redirect(APP_URL);
}

$conn = require_once "../inc/db.php";
$product_id = $_GET["product_id"];
$productDetail = Product::getProductById($conn, $product_id);
print_r($productDetail);
?>
<div id="main-content" class="main-content">
    <div id="product-detail">
        <div class="container">
            <div class="row">
                <div class="col">
                    <div class="dots-append-container py-4">
                        <!-- Appends dots of slider and customize it to button -->
                    </div>
                </div>
                <div class="col">
                    <div class="checkout-container py-4 d-flex align-items-center justify-content-end gap-3">
                        <div class="product-detail__quantity d-flex align-items-center">
                            <p class="product-detail__quantity__des m-0">On Sale from&nbsp;</p>
                            <span class="product-detail__quantity__price"> $3,299.00 </span>
                            <input class="product-detail__quantity__input ms-2" id="product-detail__quantity__input" type="number" name="quantity" value="1" min="1" pattern="[1-9]*" />
                        </div>
                        <button class="add-to-cart">
                            <span class="add-to-cart__text"> Add to Cart </span>
                        </button>
                        <button class="payment-paypal">
                            <img class="payment-paypal__img" src="<?php echo APP_URL; ?>/assets/img/payment.svg" alt="pay-with-paypal" />
                        </button>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-8">
                    <div class="product-desc-container">
                        <div class="product-desc d-flex flex-column gap-3">
                            <h3 class="product-desc__title mt"><?php echo $productDetail->name; ?></h3>
                            <p class="product-desc__desc m-0">
                                <?php echo $productDetail->description; ?> is a high-performance, high-definition, high-quality, high-speed,
                                high-definition, high-performance and high-definition graphics card that delivers a
                                performance boost to games and applications.MSI MPG Trident 3 is a high-performance,
                                high-definition, high-quality, high-speed, high-definition, high-performance and
                                high-definition graphics card that delivers a performance boost to games and
                                applications.
                            </p>
                        </div>

                        <div class="product-desc d-flex flex-column gap-3">
                            <h3 class="product-desc__title mt"><?php echo $productDetail->name; ?></h3>
                            <ul>
                                <li class="product-desc__list-item">
                                    <p class="product-desc__detail m-0">Screen: <?php echo $productDetail->screen; ?></p>
                                </li>
                                <li class="product-desc__list-item">
                                    <p class="product-desc__detail m-0">Operating system: <?php echo $productDetail->operatingSystem; ?></p>
                                </li>
                                <li class="product-desc__list-item">
                                    <p class="product-desc__detail m-0">Processor: <?php echo $productDetail->processor; ?></p>
                                </li>
                                <li class="product-desc__list-item">
                                    <p class="product-desc__detail m-0">Ram: <?php echo $productDetail->ram; ?></p>
                                </li>
                                <li class="product-desc__list-item">
                                    <p class="product-desc__detail m-0">Storage capacity: <?php echo $productDetail->storageCapacity; ?></p>
                                </li>
                                <li class="product-desc__list-item">
                                    <p class="product-desc__detail m-0">Weight: <?php echo $productDetail->weight; ?></p>
                                </li>
                                <li class="product-desc__list-item">
                                    <p class="product-desc__detail m-0">Battery Capacity: <?php echo $productDetail->batteryCapacity; ?></p>
                                </li>
                                <li class="product-desc__list-item">
                                    <p class="product-desc__detail m-0">Color: <?php echo $productDetail->color; ?></p>
                                </li>
                            </ul>
                        </div>

                        <div class="product-desc d-flex flex-column gap-3">
                            <h3 class="product-desc__title mt"><?php echo $productDetail->name ?></h3>
                            <div class="product-desc__specs">
                                <div class="product-desc__specs__detail d-flex align-items-center">
                                    <p class="product-desc__specs__detail__title m-0 py-1 ps-1">CPU</p>
                                    <p class="product-desc__specs__detail__value m-0"><?php echo $productDetail->processor ?></p>
                                </div>
                                <div class="product-desc__specs__detail d-flex align-items-center">
                                    <p class="product-desc__specs__detail__title m-0 py-1 ps-1">Features</p>
                                    <p class="product-desc__specs__detail__value m-0">N/A</p>
                                </div>
                                <div class="product-desc__specs__detail d-flex align-items-center">
                                    <p class="product-desc__specs__detail__title m-0 py-1 ps-1">I/O Ports</p>
                                    <p class="product-desc__specs__detail__value m-0">N/A</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-4">
                    <div class="product-img-container">
                        <img src="<?php echo $productDetail->imageUrl; ?>" class="object-fit-contain product-img" alt="product" />
                        <div class="add-on d-flex justify-content-center align-items-center gap-3">
                            <img src="<?php echo APP_URL; ?>/assets/img/zip.svg" alt="zip" />
                            <img src="<?php echo APP_URL; ?>/assets/img/vector.svg" alt="vector" />
                            <p class="add-on__desc m-0">
                                own it now, up to 6 months<br />
                                interest free learn more
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php require_once "../inc/components/footer.php"; ?>
<script src="<?php echo APP_URL; ?>/js/header/dropdown.js"></script>
<script src="<?php echo APP_URL; ?>/js/header/searchbar.js"></script>
<script src="<?php echo APP_URL; ?>/js/body/product-detail.js"></script>