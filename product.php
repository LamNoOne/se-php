<?php require_once "inc/components/header.php"; ?>

<?php
$conn = require_once "inc/db.php";
$allProducts = Product::getAllProducts($conn, 30);
// print_r($allProducts);
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
                        <img class="promotion-img object-fit-fill" src="assets/img/promotion_2.jpg" alt="promotion" />
                    </div>
                    <div class="promotion-container">
                        <img class="promotion-img object-fit-fill" src="assets/img/promotion_3.jpg" alt="promotion" />
                    </div>
                    <div class="promotion-container">
                        <img class="promotion-img object-fit-fill" src="assets/img/promotion_4.jpg" alt="promotion" />
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="product-card-container">
        <!-- Description product page and control button sort, grid layout -->
        <div class="container">
            <div class="row">
                <div class="col-2">
                    <div class="product-filter">
                        <div class="category-filter pb-3">
                            <h6 class="filter-title">Category</h6>
                            <ul class="category-list list-unstyled d-flex flex-column gap-1 m-0">
                                <li class="category-item"><a class="active" href="#smartphone">Smartphone</a></li>
                                <li class="category-item"><a class="" href="#laptop">Laptop</a></li>
                                <li class="category-item"><a class="" href="#accessory">Accessory</a></li>
                                <li class="category-item"><a class="" href="#studio">Studio</a></li>
                                <li class="category-item"><a class="" href="#camera">Camera</a></li>
                                <li class="category-item"><a class="" href="#pc">PC</a></li>
                                <li class="category-item"><a class="" href="#tv">TV</a></li>
                                <li class="category-item"><a class="" href="#product">Product</a></li>
                            </ul>
                        </div>

                        <div class="price-filter py-3">
                            <h6 class="filter-title">Price</h6>
                            <form action="" method="" class="d-flex gap-1">
                                <input class="price-filter--min" placeholder="Min" type="number" name="min" id="min-number" min="0" pattern="[0-9]*" />
                                <span>-</span>
                                <input class="price-filter--max" placeholder="Max" type="number" name="max" id="max-number" min="0" pattern="[0-9]*" />
                                <button class="price-filter__btn-price" type="button">
                                    <i class="fa-solid fa-play"></i>
                                </button>
                            </form>
                        </div>

                        <div class="check-filter py-3">
                            <h6 class="filter-title">Operating System</h6>
                            <ul class="input-container list-unstyled d-flex flex-column gap-2 m-0">
                                <li class="d-flex align-items-center gap-2 filter-item">
                                    <input type="checkbox" value="android" name="android" id="android" /><label for="android">Android</label>
                                </li>
                                <li class="d-flex align-items-center gap-2 filter-item">
                                    <input type="checkbox" value="ios" name="ios" id="ios" /><label for="ios">iOS</label>
                                </li>
                                <li class="d-flex align-items-center gap-2 filter-item">
                                    <input type="checkbox" value="macos" name="macos" id="macos" /><label for="macos">MacOS</label>
                                </li>
                                <li class="d-flex align-items-center gap-2 filter-item">
                                    <input type="checkbox" value="windows" name="windows" id="windows" /><label for="windows">Windows</label>
                                </li>
                                <li class="d-flex align-items-center gap-2 filter-item">
                                    <input type="checkbox" value="others" name="others" id="others" /><label for="others">Others</label>
                                </li>
                            </ul>
                        </div>

                        <div class="check-filter py-3">
                            <h6 class="filter-title">RAM memory</h6>
                            <ul class="input-container list-unstyled d-flex flex-column gap-2 m-0">
                                <li class="d-flex align-items-center gap-2 filter-item">
                                    <input type="checkbox" value="16" name="ram" id="ram_16" /><label for="ram_16">16GB</label>
                                </li>
                                <li class="d-flex align-items-center gap-2 filter-item">
                                    <input type="checkbox" value="8" name="ram" id="ram_8" /><label for="ram_8">8GB</label>
                                </li>
                                <li class="d-flex align-items-center gap-2 filter-item">
                                    <input type="checkbox" value="4" name="ram" id="ram_4" /><label for="ram_4">4GB</label>
                                </li>
                                <li class="d-flex align-items-center gap-2 filter-item">
                                    <input type="checkbox" value="6" name="ram" id="ram_6" /><label for="ram_6">6GB</label>
                                </li>
                                <li class="d-flex align-items-center gap-2 filter-item">
                                    <input type="checkbox" value="3" name="ram" id="ram_3" /><label for="ram_3">3GB</label>
                                </li>
                                <li class="d-flex align-items-center gap-2 filter-item">
                                    <input type="checkbox" value="2" name="ram" id="ram_2" /><label for="ram_2">2GB</label>
                                </li>
                                <li class="d-flex align-items-center gap-2 filter-item">
                                    <input type="checkbox" value="128" name="ram" id="ram_128" /><label for="ram_128">128GB</label>
                                </li>
                                <li class="d-flex align-items-center gap-2 filter-item">
                                    <input type="checkbox" value="1" name="ram" id="ram_1" /><label for="ram_1">1GB</label>
                                </li>
                                <li class="d-flex align-items-center gap-2 filter-item">
                                    <input type="checkbox" value="32" name="ram" id="ram_32" /><label for="ram_32">32GB</label>
                                </li>
                            </ul>
                        </div>

                        <div class="check-filter py-3">
                            <h6 class="filter-title">Video Resolution</h6>
                            <ul class="input-container list-unstyled d-flex flex-column gap-2 m-0">
                                <li class="d-flex align-items-center gap-2 filter-item">
                                    <input type="checkbox" value="1080p" name="resolution" id="resolution_1080p" /><label for="resolution_1080p">1080p</label>
                                </li>
                                <li class="d-flex align-items-center gap-2 filter-item">
                                    <input type="checkbox" value="720p" name="resolution" id="resolution_720p" /><label for="resolution_720p">720p</label>
                                </li>
                                <li class="d-flex align-items-center gap-2 filter-item">
                                    <input type="checkbox" value="2160p" name="resolution" id="resolution_2160p" /><label for="resolution_2160p">2160p</label>
                                </li>
                                <li class="d-flex align-items-center gap-2 filter-item">
                                    <input type="checkbox" value="4k" name="resolution" id="resolution_4k" /><label for="resolution_4k">4k</label>
                                </li>
                                <li class="d-flex align-items-center gap-2 filter-item">
                                    <input type="checkbox" value="480p" name="resolution" id="resolution_480p" /><label for="resolution_480p">480p</label>
                                </li>
                                <li class="d-flex align-items-center gap-2 filter-item">
                                    <input type="checkbox" value="8k" name="resolution" id="resolution_8k" /><label for="resolution_8k">8k</label>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-10">
                    <div class="row position-relative mb-5">
                        <div class="col">
                            <div class="page-product-desc d-flex align-items-center">
                                <p class="m-0">Items <span>1</span>-<span>35</span> of <span>61</span></p>
                            </div>
                        </div>
                        <div class="col position-absolute z-1">
                            <div class="page-product-control d-flex justify-content-end align-items-baseline gap-4">
                                <div class="btn-sort-product">
                                    <div class="title">Sort by:&nbsp;</div>
                                    <div class="sel">
                                        <div class="label"></div>
                                        <div class="options">
                                            <div data-value="default" class="selected">Best Match</div>
                                            <div data-value="asc">Price Low to High</div>
                                            <div data-value="desc">Price High to Low</div>
                                        </div>
                                    </div>
                                </div>

                                <div class="btn-grid-control d-flex align-items-center gap-3">
                                    <h6 class="grid-control-title m-0">View:</h6>
                                    <button class="grid-system bg-transparent"></button>

                                    <button class="grid-horizontal bg-transparent blur"></button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <?php foreach ($allProducts as $product) : ?>
                            <div class="mb-2 col col-sm-6 col-lg-4 col-xl-3">
                                <div class="card-product-detail">
                                    <div class="item-status d-flex">
                                        <img src="assets/img/stock.svg" alt="in-stock" />
                                        <span class="true">&nbsp;in stock</span>
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
                                            <?php echo $product->description; ?>
                                        </p>
                                    </div>
                                    <div class="price-container d-flex flex-column align-items-start">
                                        <span class="old-price">$<?php echo $product->price; ?></span>
                                        <span class="new-price">$<?php echo $product->price; ?></span>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="row">
                        <div class="pagination justify-content-end py-3">
                            <a href="#">&laquo;</a>
                            <a href="#">1</a>
                            <a href="#" class="active">2</a>
                            <a href="#">3</a>
                            <a href="#">4</a>
                            <a href="#">5</a>
                            <a href="#">6</a>
                            <a href="#">&raquo;</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="brand">
        <div class="container">
            <div class="d-flex justify-content-between overflow-hidden">
                <a href="#" class="d-inline-flex">
                    <img src="/assets/img/brand_1.png" alt="brand" class="item-brand object-fit-contain" />
                </a>

                <a href="#" class="d-inline-flex">
                    <img src="/assets/img/brand_2.png" alt="brand" class="item-brand object-fit-contain" />
                </a>

                <a href="#" class="d-inline-flex">
                    <img src="/assets/img/brand_3.png" alt="brand" class="item-brand object-fit-contain" />
                </a>

                <a href="#" class="d-inline-flex">
                    <img src="/assets/img/brand_4.png" alt="brand" class="item-brand object-fit-contain" />
                </a>

                <a href="#" class="d-inline-flex">
                    <img src="/assets/img/brand_5.png" alt="brand" class="item-brand object-fit-contain" />
                </a>

                <a href="#" class="d-inline-flex">
                    <img src="/assets/img/brand_6.png" alt="brand" class="item-brand object-fit-contain" />
                </a>

                <a href="#" class="d-inline-flex">
                    <img src="/assets/img/brand_7.png" alt="brand" class="item-brand object-fit-contain" />
                </a>
            </div>
        </div>
    </div>
</div>

<?php require_once "inc/components/footer.php"; ?>
<script>
    // onClick => fetch url => response => innerHtml => change url.
    // read url => take effect on checkbox
    const inputContainers = document.querySelectorAll(".input-container");
    inputContainers.forEach((inputContainer) => {
        inputContainer.addEventListener("click", (event) => {
            if (event.target.attributes?.type?.value === "checkbox" && event.target.checked) {
                const key = event.target.name;
                const value = event.target.value;
                console.log({
                    key: value,
                });
            }
            // console.dir(event.target)
        });
    });
</script>

<script src="<?php echo APP_URL; ?>/js/header/dropdown.js"></script>
<script src="<?php echo APP_URL; ?>/js/header/searchbar.js"></script>
<script src="<?php echo APP_URL; ?>/js/body/dropdown.js"></script>
<script src="<?php echo APP_URL; ?>/js/body/promotion.js"></script>