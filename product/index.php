<?php require_once "../inc/components/header.php"; ?>
<?php require_once "../inc/utils.php"; ?>
<?php
// Initialize an array to store the parsed parameters
$parsedParams = array();

// Check if there are query parameters in the URL
if (!empty($_GET)) {
    // Get connection
    $conn = require_once "../inc/db.php";
    // Loop through each parameter
    foreach ($_GET as $key => $value) {
        // Check if the value contains commas
        if ($key !== 'page' && $key !== 'orderby') {
            if (strpos($value, ',') !== false) {
                // If it does, split the value into an array using commas as separators
                $parsedParams[$key] = explode(',', $value);
                // Trim each value to remove leading/trailing spaces
                $parsedParams[$key] = array_map('trim', $parsedParams[$key]);
            } else {
                // If it doesn't contain commas, use the value as is
                $parsedParams[$key] = $value;
            }
        }
    }

    // Set default values
    $offset = 0;
    $limit = 20;
    $orderBy = '';

    if (isset($_GET['page'])) {
        $offset = ($_GET['page'] - 1) * $limit;
    }

    if (isset($_GET['orderby'])) {
        $orderBy = $_GET['orderby'];
    }

    $selectors = [
        'fields' => '*',
        'filters' => [],
        'orderBy' => $orderBy,
        'limit' => 20,
        'offset' => $offset,
    ];

    if (!empty($parsedParams)) {
        // Apply filters
        $selectors['filters'] = $parsedParams;
        // Get product object
        $selectedProducts = Product::getProductsByCategory($conn, $selectors);
        // Get all products
        $allProducts = $selectedProducts['data'];
        // Get total pages
        $allPages = $selectedProducts['totalPage'];
    }
} else {
    // If url is not valid
    redirect(APP_URL);
}
?>

<div id="main-content" class="main-content">
    <div id="promotion-slider">
        <div class="container">
            <div class="row">
                <div class="single-promotion position-relative">
                    <div class="promotion-container">
                        <img class="promotion-img object-fit-fill" src="<?php echo APP_URL; ?>/assets/img/promotion.png" alt="promotion" />
                    </div>
                    <div class="promotion-container">
                        <img class="promotion-img object-fit-fill" src="<?php echo APP_URL; ?>/assets/img/promotion_2.jpg" alt="promotion" />
                    </div>
                    <div class="promotion-container">
                        <img class="promotion-img object-fit-fill" src="<?php echo APP_URL; ?>/assets/img/promotion_3.jpg" alt="promotion" />
                    </div>
                    <div class="promotion-container">
                        <img class="promotion-img object-fit-fill" src="<?php echo APP_URL; ?>/assets/img/promotion_4.jpg" alt="promotion" />
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
                                <li class="category-item"><a class="<?php echo (verifyCategory($_GET['categoryId'], '1') ? 'active' : '') ?>" href="<?php echo APP_URL; ?>/product?categoryId=1">Smartphone</a></li>
                                <li class="category-item"><a class="<?php echo (verifyCategory($_GET['categoryId'], '2') ? 'active' : '') ?>" href="<?php echo APP_URL; ?>/product?categoryId=2">Laptop</a></li>
                                <li class="category-item"><a class="<?php echo (verifyCategory($_GET['categoryId'], '3') ? 'active' : '') ?>" href="<?php echo APP_URL; ?>/product?categoryId=3">Accessory</a></li>
                                <li class="category-item"><a class="<?php echo (verifyCategory($_GET['categoryId'], '4') ? 'active' : '') ?>" href="<?php echo APP_URL; ?>/product?categoryId=4">Studio</a></li>
                                <li class="category-item"><a class="<?php echo (verifyCategory($_GET['categoryId'], '5') ? 'active' : '') ?>" href="<?php echo APP_URL; ?>/product?categoryId=5">Camera</a></li>
                                <li class="category-item"><a class="<?php echo (verifyCategory($_GET['categoryId'], '6') ? 'active' : '') ?>" href="<?php echo APP_URL; ?>/product?categoryId=6">PC</a></li>
                                <li class="category-item"><a class="<?php echo (verifyCategory($_GET['categoryId'], '7') ? 'active' : '') ?>" href="<?php echo APP_URL; ?>/product?categoryId=7">TV</a></li>
                                <li class="category-item"><a class="<?php echo (verifyCategory($_GET['categoryId'], '8') ? 'active' : '') ?>" href="<?php echo APP_URL; ?>/product?categoryId=8">Product</a></li>
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
                            <ul class="list-unstyled d-flex flex-column gap-2 m-0">
                                <li class=" input-container d-flex align-items-center gap-2 filter-item">
                                    <input type="checkbox" value="Android" name="operatingSystem" id="android" /><label for="android">Android</label>
                                </li>
                                <li class="input-container d-flex align-items-center gap-2 filter-item">
                                    <input type="checkbox" value="MacOS" name="operatingSystem" id="macOS" /><label for="macOS">MacOS</label>
                                </li>
                                <li class="input-container d-flex align-items-center gap-2 filter-item">
                                    <input type="checkbox" value="Logitech" name="operatingSystem" id="logitech" /><label for="logitech">Logitech</label>
                                </li>
                                <li class="input-container d-flex align-items-center gap-2 filter-item">
                                    <input type="checkbox" value="Apple" name="operatingSystem" id="apple" /><label for="apple">Apple</label>
                                </li>
                                <li class="input-container d-flex align-items-center gap-2 filter-item">
                                    <input type="checkbox" value="Windows" name="operatingSystem" id="windows" /><label for="windows">Windows</label>
                                </li>
                                <li class="input-container d-flex align-items-center gap-2 filter-item">
                                    <input type="checkbox" value="Cisco" name="operatingSystem" id="cisco" /><label for="cisco">Cisco</label>
                                </li>
                                <li class="input-container d-flex align-items-center gap-2 filter-item">
                                    <input type="checkbox" value="Samsung" name="operatingSystem" id="samsung" /><label for="samsung">Samsung</label>
                                </li>
                                <li class="input-container d-flex align-items-center gap-2 filter-item">
                                    <input type="checkbox" value="Asus" name="operatingSystem" id="asus" /><label for="asus">Asus</label>
                                </li>
                                <li class="input-container d-flex align-items-center gap-2 filter-item">
                                    <input type="checkbox" value="JBL" name="operatingSystem" id="jbl" /><label for="jbl">JBL</label>
                                </li>
                                <li class="input-container d-flex align-items-center gap-2 filter-item">
                                    <input type="checkbox" value="Camera" name="operatingSystem" id="camera" /><label for="camera">Camera</label>
                                </li>
                            </ul>
                        </div>

                        <div class="check-filter py-3">
                            <h6 class="filter-title">RAM memory</h6>
                            <ul class="list-unstyled d-flex flex-column gap-2 m-0">
                                <li class="input-container d-flex align-items-center gap-2 filter-item">
                                    <input type="checkbox" value="1" name="ram" id="ram_1" /><label for="ram_1">1GB</label>
                                </li>
                                <li class="input-container d-flex align-items-center gap-2 filter-item">
                                    <input type="checkbox" value="4" name="ram" id="ram_4" /><label for="ram_4">4GB</label>
                                </li>
                                <li class="input-container d-flex align-items-center gap-2 filter-item">
                                    <input type="checkbox" value="32" name="ram" id="ram_32" /><label for="ram_32">32GB</label>
                                </li>
                            </ul>
                        </div>

                        <div class="check-filter py-3">
                            <h6 class="filter-title">Video Resolution</h6>
                            <ul class="list-unstyled d-flex flex-column gap-2 m-0">
                                <li class="input-container d-flex align-items-center gap-2 filter-item">
                                    <input type="checkbox" value="FullHD | IPS" name="screen" id="resolution_1080p" /><label for="resolution_1080p">FullHD | IPS</label>
                                </li>
                                <li class="input-container d-flex align-items-center gap-2 filter-item">
                                    <input type="checkbox" value="4K | IPS" name="screen" id="resolution_720p" /><label for="resolution_720p">4K | IPS</label>
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
                                        <div class="options" id="sort-product">
                                            <div data-value="default" class="selected">Best Match</div>
                                            <div data-value="asc">Price Low to High</div>
                                            <div data-value="desc">Price High to Low</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <?php foreach ($allProducts as $product) : ?>
                            <div class="mb-2 col col-sm-6 col-lg-4 col-xl-3">
                                <div class="card-product-detail" data-index="<?php echo $product->id; ?>">
                                    <div class="item-status d-flex">
                                        <img src="<?php echo APP_URL; ?>/assets/img/stock.svg" alt="in-stock" />
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
                        <div id="pagination" class="py-3"></div>
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

<?php require_once "../inc/components/footer.php"; ?>
<script>
    let orderBy = "<?php echo $orderBy; ?>" === "" ? "default" : "<?php echo $orderBy; ?>"
</script>
<script src="<?php echo APP_URL; ?>/js/header/dropdown.js"></script>
<script src="<?php echo APP_URL; ?>/js/header/searchbar.js"></script>
<script src="<?php echo APP_URL; ?>/js/body/dropdown.js"></script>
<script src="<?php echo APP_URL; ?>/js/body/promotion.js"></script>
<script src="<?php echo APP_URL; ?>/assets/pagination/pagination.js"></script>
<script>
    /**
     * Handle click events on card product
     */
    const productCards = document.querySelectorAll('.card-product-detail');
    productCards.forEach(productCard => {
        productCard.addEventListener('click', () => {
            window.location.href = `<?php echo APP_URL; ?>/product/product-detail.php?product_id=${productCard.dataset.index}`;
        })
    })
</script>
<script>
    // Initialize new object container selectors
    const selector = {};

    // Define default selectors params
    const checkParams = ['operatingSystem', 'ram', 'screen'];

    // Select all elements with class "input-container"
    const inputContainers = document.querySelectorAll(".input-container");

    // Select all input with type checkbox
    const inputChecks = document.querySelectorAll("input[type=checkbox]");

    // Construct the base URL
    const baseUrl = "http://localhost/se-php/product/";

    // Function to convert object to query string
    function objectToQueryString(obj) {
        const queryString = Object.keys(obj)
            .map(key => `${encodeURIComponent(key)}=${encodeURIComponent(obj[key])}`)
            .join("&");
        return queryString;
    }

    function navigateTo(baseUrl, selector) {
        // Construct the full URL with query parameters
        const fullUrl = baseUrl + "?" + objectToQueryString(selector);

        // Navigate to url
        window.location.href = fullUrl;
    }


    // Check if there are query parameters in the URL
    if (window.location.href.split("?")[1]) {
        // Get the query string part of the URL
        const strSelector = window.location.href.split("?")[1];

        // Split the query string by "&" to get individual key-value pairs
        const pairs = strSelector.split("&");

        // Iterate over each key-value pair
        pairs.forEach((pair) => {
            // Split each pair by "=" to separate key and value
            const [key, value] = pair.split("=");

            // Decode URI component to handle special characters properly
            // Initialize an array for each key and store the value in it
            selector[key] = new Array(decodeURIComponent(value))[0].split(",");
        });

        // Output the selector object after initializing it with query parameters
        inputChecks.forEach(inputCheck => {
            Object.keys(selector).forEach(key => {
                if (checkParams.includes(String(key)) && selector[key].includes(inputCheck.value)) {
                    inputCheck.checked = true;
                }
            })
        })
    }

    /** Fix refresh page  */

    // Add click event listener to each input container
    inputContainers.forEach((inputContainer) => {
        inputContainer.addEventListener("click", (event) => {
            // Check if the clicked element is a checkbox
            if (event.target.attributes.type.value === "checkbox") {
                event.stopPropagation();
                const key = event.target.name; // Get the name attribute of the checkbox
                const value = event.target.value; // Get the value attribute of the checkbox
                // Check if the key already exists in the selector object and selector[key] is already array
                if (Array.isArray(selector[key])) {
                    // If value already exists, mean user click ready checkbox
                    if (selector[key].includes(value)) {
                        // If selector has one value, delete all info from selector
                        // otherwise, remove one element from selector
                        selector[key].length === 1 ? delete selector[key] : selector[key].splice(selector[key].indexOf(value), 1);
                    } else {
                        // If value doesn't exist, add it to selector
                        selector[key].push(value);
                    }
                } else {
                    // If key doesn't exist, initialize a new array with the value
                    selector[key] = new Array(value);
                }
                navigateTo(baseUrl, selector);
            }
        });
    });

    /**
     * Pagination Handler
     */

    const allPages = parseInt(<?php echo $allPages; ?>);
    var init = function() {
        Pagination.Init(document.getElementById("pagination"), {
            size: allPages, // pages size
            page: 1, // selected page
            step: 1, // pages before and after current
        });
    };

    // Bind pagination when page is mounted
    document.addEventListener("DOMContentLoaded", init, false);
    // Get pagination HTML element
    const pagination = document.getElementById("pagination");

    // If current page is not specified, set default page equal to '1'
    let currentPage = <?php echo isset($_GET['page']) ? $_GET['page'] : 1; ?>;
    // Override default method, follow to url parameters
    Pagination.Bind = function() {
        var a = Pagination.e.getElementsByTagName('a');
        for (var i = 0; i < a.length; i++) {
            if (+a[i].innerHTML == currentPage) a[i].className = 'current';
            a[i].addEventListener('click', Pagination.Click, false);
        }
    }

    // Handle pagination click event
    pagination.addEventListener("click", () => {
        // Set page = clicked value
        selector['page'] = Pagination.page;
        // Navigate with clicked page
        navigateTo(baseUrl, selector);
    })
</script>