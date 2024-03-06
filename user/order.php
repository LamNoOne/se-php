<?php require_once "../inc/components/header.php"; ?>
<?php require_once "../inc/init.php"; ?>

<?php
Auth::requireLogin();
if (!isset($conn))
    $conn = require_once "../inc/conn.php";

$allOrdersData = Order::getOrderByUserId($conn, $_SESSION['userId']);
$allOrders = $allOrdersData['data'];
$allPages = $allOrdersData['totalPage'];

// print_r($allOrders);
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
                <ul class="list-unstyled d-flex flex-column gap-3">
                    <?php foreach ($allOrders as $orders) : ?>
                        <li>
                            <div class="order-item d-flex flex-column">
                                <ul class="d-flex flex-column flex-grow-1 gap-3">
                                    <?php foreach($orders->orderDetail as $order) : ?>
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
                                <a href="<?php echo APP_URL; ?>/payment/transaction.php?checkout_ref_id=<?php echo base64_encode($orders->transaction_id); ?>" class="btn-order-detail align-self-end text-decoration-none d-flex justify-content-center align-items-center mt-3">Watch Detail</a>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </div>
</div>

<?php require_once "../inc/components/footer.php"; ?>
<script src="<?php echo APP_URL; ?>/js/header/dropdown.js"></script>
<script src="<?php echo APP_URL; ?>/js/header/searchbar.js"></script>