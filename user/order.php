<?php require_once "../inc/components/header.php"; ?>
<?php require_once "../inc/init.php"; ?>
<?php require_once "../inc/utils.php"; ?>

<?php
Auth::requireLogin();
if (!isset($conn))
    $conn = require_once "../inc/conn.php";

$limit = 4;
$offset = 0;

if (isset($_GET['page'])) {
    $offset = ($_GET['page'] - 1) * $limit;
}

$allOrdersData = Order::getOrderByUserId($conn, $_SESSION['userId'], $limit, $offset);
$allOrders = $allOrdersData['data'];
$allPages = $allOrdersData['totalPage'];
?>

<div id="customer-manager">
    <div class="container">
        <div class="row">
            <div class="col-2">
                <ul class="list-unstyled customer-manager__btn-list">
                    <li>
                        <a href="<?php echo APP_URL; ?>/user/" class="customer-manager__btn-list__item text-decoration-none text-black">
                            <img class="object-fit-contain" src="../assets/img/person.svg" alt="account">
                            <span>Your Account</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo APP_URL; ?>/user/order.php" class="customer-manager__btn-list__item text-decoration-none text-black">
                            <img class="object-fit-contain" src="../assets/img/history.svg" alt="history">
                            <span>Order History</span>
                        </a>
                    </li>
                    <li>
                        <button type="button" class="customer-manager__btn-list__item">
                            <img class="object-fit-contain" src="../assets/img/exit.svg" alt="logout">
                            <span>Log Out</span>
                        </button>
                    </li>
                </ul>
            </div>
            <div class="col-10 px-4">
                <?php if(!empty($allOrders)) : ?>
                <ul class="list-unstyled d-flex flex-column gap-3">
                    <?php foreach ($allOrders as $orders) : ?>
                        <li>
                            <div class="order-item d-flex flex-column">
                                <ul class="d-flex flex-column flex-grow-1 gap-3">
                                    <?php foreach ($orders->orderDetail as $order) : ?>
                                        <li class="d-flex justify-content-between w-100 border-bottom border-black border-opacity-10 py-2">
                                            <div class="d-flex gap-3">
                                                <img class="order-item__img object-fit-contain" src="<?php echo $order->imageUrl; ?>" alt="order-item" />
                                                <div class="order-item__info d-flex flex-column justify-content-between">
                                                    <p class="order-item__desc m-0">
                                                        <?php echo $order->name; ?>
                                                    </p>
                                                    <span>&times;<?php echo $order->quantity; ?></span>
                                                    <span class="order-item__price"><?php echo $order->price; ?> USD</span>
                                                </div>
                                            </div>
                                            <div class="order-item__time d-flex flex-column justify-content-between">
                                                <span><?php echo $order->createdAt; ?></span>
                                                <span class="subtotal-price"><?php echo $order->quantity * $order->price; ?> USD</span>
                                            </div>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span style="background-color: <?php echo getStatus($orders->status); ?>;" class="order-status border border-black border-opacity-10 py-2 px-3 text-black text-opacity-50 text-decoration-none d-flex justify-content-center align-items-center mt-3">Status: <?php echo $orders->status; ?></span>
                                    <div class="order-btn-control d-flex gap-3 justify-content-end align-items-center">
                                        <button data-index="<?php echo $orders->id; ?>" class="btn-order-detail align-self-end text-decoration-none d-flex justify-content-center align-items-center mt-3">Repurchase</button>
                                        <?php if (!$orders->transaction_id) : ?>
                                            <a href="<?php echo APP_URL; ?>/payment?orderId=<?php echo $orders->id; ?>" class="continue-payment align-self-end text-decoration-none d-flex justify-content-center align-items-center mt-3">Continue Payment</a>
                                        <?php else : ?>
                                            <a href="<?php echo APP_URL; ?>/payment/transaction.php?checkout_ref_id=<?php echo base64_encode($orders->transaction_id); ?>" class="btn-watch-detail border border-black border-opacity-10 py-2 px-3 text-black text-opacity-50 text-decoration-none d-flex justify-content-center align-items-center mt-3">Watch Detail</a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
                <div class="row">
                    <div id="pagination" class="py-3"></div>
                </div>
                <?php else : ?>
                    <div class="row">
                    <div class="no-cart d-flex flex-column justify-content-center align-items-center">
                        <div class="no-cart__img">
                            <img class="object-fit-contain" src="https://deo.shopeemobile.com/shopee/shopee-pcmall-live-sg/cart/9bdd8040b334d31946f4.png" alt="no-cart">
                        </div>
                        <p class="no-cart__desc">There are no orders</p>
                        <a href="<?php echo APP_URL; ?>" class="no-cart__btn text-decoration-none">Continue to shopping</a>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php require_once "../inc/components/footer.php"; ?>
<script src="<?php echo APP_URL; ?>/assets/pagination/pagination.js"></script>
<script src="<?php echo APP_URL; ?>/js/header/dropdown.js"></script>
<script src="<?php echo APP_URL; ?>/js/header/searchbar.js"></script>

<script>
    // Create cart from existed order
    $(document).ready(function() {
        const allOrderBtnRepurchase = $(".btn-order-detail").get();
        console.log(allOrderBtnRepurchase)
        allOrderBtnRepurchase.forEach(function(el) {
            el.addEventListener("click", async function(e) {
                e.preventDefault();
                const orderId = $(this).data("index");

                try {
                    const repurchaseResponse = await $.ajax({
                        method: "POST",
                        url: "actions/repurchase.php",
                        data: {
                            orderId
                        }
                    })
                    const {
                        status,
                        message
                    } = JSON.parse(repurchaseResponse);
                    status ? window.location.href = "<?php echo APP_URL; ?>/cart" : toastr.error(message, "Error Repurchase");
                } catch (error) {
                    toastr.error(error.message, "Error");
                }
            })
        })
    })
</script>
<script>
    const selector = {};
    const baseUrl = "<?php echo APP_URL; ?>/user/order.php";

    // Function to convert object to filtered query string
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
        // console.log(`PAIR::${pairs}`)

        // Iterate over each key-value pair
        pairs.forEach((pair) => {
            // Split each pair by "=" to separate key and value
            const [key, value] = pair.split("=");
            // console.log(`KEY::${key} VALUE::${value}`)

            // Decode URI component to handle special characters properly
            // Initialize an array for each key and store the value in selectors array
            selector[key] = new Array(decodeURIComponent(value))[0].split(",");
        });

        console.log(selector)
    }
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