<?php require_once "../inc/components/header.php"; ?>
<?php require_once "../inc/init.php"; ?>

<?php
Auth::requireLogin();
if (!isset($conn))
    $conn = require_once "../inc/conn.php";

$allOrders = Order::getOrderByUserId($conn, $_SESSION['userId']);

print_r($allOrders);
?>

<div id="customer-manager">
    <div class="container">
        <div class="row">
            <div class="col-2">
                <ul class="list-unstyled customer-manager__btn-list">
                    <li>
                        <button type="button" class="customer-manager__btn-list__item">
                            <img class="object-fit-contain" src="../assets/img/person.svg" alt="account">
                            <span>Your Account</span>
                        </button>
                    </li>
                    <li>
                        <button type="button" class="customer-manager__btn-list__item">
                            <img class="object-fit-contain" src="../assets/img/history.svg" alt="history">
                            <span>Order History</span>
                        </button>
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
                    <li>
                        <div class="order-item d-flex justify-content-between">
                            <div class="d-flex gap-3">
                                <img class="order-item__img object-fit-contain" src="../assets/img/desk_2.png" alt="order-item" />
                                <div class="order-item__info d-flex flex-column justify-content-between">
                                    <p class="order-item__desc m-0">
                                        MSI MPG Trident 3 10SC-005AU Intel i7 10700F, 2060 SUPER, 16GB RAM
                                    </p>
                                    <span class="order-item__price">1.246.000USD</span>
                                </div>
                            </div>
                            <div class="order-item__time d-flex flex-column justify-content-between">
                                <span>01/11/2023 14:24</span>
                                <button class="btn-order-detail">Watch Detail</button>
                            </div>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

<?php require_once "../inc/components/footer.php"; ?>
<script src="<?php echo APP_URL; ?>/js/header/dropdown.js"></script>
<script src="<?php echo APP_URL; ?>/js/header/searchbar.js"></script>