<?php require_once "inc/components/header.php"; ?>
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
                            <img class="payment-paypal__img" src="assets/img/payment.svg" alt="pay-with-paypal" />
                        </button>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-8">
                    <div class="product-desc-container">
                        <div class="product-desc d-flex flex-column gap-3">
                            <h3 class="product-desc__title mt">MSI MPG Trident 3</h3>
                            <p class="product-desc__desc m-0">
                                MSI MPG Trident 3 is a high-performance, high-definition, high-quality, high-speed,
                                high-definition, high-performance and high-definition graphics card that delivers a
                                performance boost to games and applications.MSI MPG Trident 3 is a high-performance,
                                high-definition, high-quality, high-speed, high-definition, high-performance and
                                high-definition graphics card that delivers a performance boost to games and
                                applications.
                            </p>
                        </div>

                        <div class="product-desc d-flex flex-column gap-3">
                            <h3 class="product-desc__title mt">MSI MPG Trident 3</h3>
                            <ul>
                                <li class="product-desc__list-item">
                                    <p class="product-desc__detail m-0">Intel Core i7-10700F</p>
                                </li>
                                <li class="product-desc__list-item">
                                    <p class="product-desc__detail m-0">Intel H410</p>
                                </li>
                                <li class="product-desc__list-item">
                                    <p class="product-desc__detail m-0">WHITE</p>
                                </li>
                                <li class="product-desc__list-item">
                                    <p class="product-desc__detail m-0">
                                        NVIDIA MSI GeForce RTX 2060 SUPER 8GB AERO ITX GDDR6
                                    </p>
                                </li>
                                <li class="product-desc__list-item">
                                    <p class="product-desc__detail m-0">SO-DIMM 16GB (16GB x 1) DDR4 2666MHz</p>
                                </li>
                                <li class="product-desc__list-item">
                                    <p class="product-desc__detail m-0">2 total slots (64GB Max)</p>
                                </li>
                                <li class="product-desc__list-item">
                                    <p class="product-desc__detail m-0">
                                        512GB (1 x 512GB) M.2 NVMe PCIe GEN3x4 SSD 2TB (2.5) 5400RPM
                                    </p>
                                </li>
                                <li class="product-desc__list-item">
                                    <p class="product-desc__detail m-0">Gaming Keyboard GK30 + Gaming Mouse GM11</p>
                                </li>
                                <li class="product-desc__list-item">
                                    <p class="product-desc__detail m-0">
                                        3.5 HDD (0/0), 2.5 HDD/SSD(1/0), M.2 (1/0)
                                    </p>
                                </li>
                                <li class="product-desc__list-item">
                                    <p class="product-desc__detail m-0">Intel WGI219Vethernet (10/100/1000M)</p>
                                </li>
                                <li class="product-desc__list-item">
                                    <p class="product-desc__detail m-0">AX200 (WIFI 6)+BT5.1</p>
                                </li>
                                <li class="product-desc__list-item">
                                    <p class="product-desc__detail m-0">PSU 330W</p>
                                </li>
                                <li class="product-desc__list-item">
                                    <p class="product-desc__detail m-0">Fan Cooler</p>
                                </li>
                            </ul>
                        </div>

                        <div class="product-desc d-flex flex-column gap-3">
                            <h3 class="product-desc__title mt">MSI MPG Trident 3</h3>
                            <div class="product-desc__specs">
                                <div class="product-desc__specs__detail d-flex align-items-center">
                                    <p class="product-desc__specs__detail__title m-0 py-1 ps-1">CPU</p>
                                    <p class="product-desc__specs__detail__value m-0">N/A</p>
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
                        <img src="assets/img/product-detail.webp" class="object-fit-cover product-img" alt="product" />
                        <div class="add-on d-flex justify-content-center align-items-center gap-3">
                            <img src="assets/img/zip.svg" alt="zip" />
                            <img src="assets/img/vector.svg" alt="" />
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
<?php require_once "inc/components/footer.php"; ?>
<script src="<?php echo APP_URL; ?>/js/header/dropdown.js"></script>
<script src="<?php echo APP_URL; ?>/js/header/searchbar.js"></script>
<script src="<?php echo APP_URL; ?>/js/body/product-detail.js"></script>