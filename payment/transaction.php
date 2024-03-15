<?php
require_once dirname(__DIR__) . "/inc/components/header.php";
require_once dirname(__DIR__) . "/inc/init.php";
require_once dirname(__DIR__) . "/inc/utils.php";

Auth::requireLogin();

// Check whether the payment ID is not empty 
if (!empty($_GET['checkout_ref_id'])) {
    $payment_txn_id  = base64_decode($_GET['checkout_ref_id']);

    // Establish a PDO connection
    if (!isset($conn))
        $conn = require_once dirname(__DIR__) . "/inc/db.php";

    // Fetch transaction data from the database
    $transactionData = Order::getOrderByTransaction($conn, $payment_txn_id)['data'];
    $transaction =  $transactionData['transaction'];
    $orderTransaction = $transactionData['orderTransaction'];
} else {
    header("Location: index.php");
    exit;
}
?>

<div class="transaction-container bg-black bg-opacity-10">
    <div class="container">
        <div class="row">
            <div class="col-1"></div>
            <div class="col-10 my-5">
                <div class="card invoice-preview-card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between flex-xl-row flex-md-column flex-sm-row flex-column p-sm-3 p-0">
                            <div class="mb-xl-0 mb-4">
                                <div class="d-flex svg-illustration mb-3 gap-2">
                                    <h1 class="fs-4 m-0">Transaction Details</h1>
                                </div>
                                <p class="mb-1"><?php echo $transaction->shipAddress; ?></p>
                                <p class="mb-1">San Diego County, CA 91905, USA</p>
                                <p class="mb-0"><?php echo $transaction->phoneNumber; ?></p>
                            </div>
                            <div>
                                <h4>Invoice #<?php echo $transaction->invoice_id ?></h4>
                                <div class="mb-2">
                                    <span class="me-1">Date Issues:</span>
                                    <span class="fw-medium"><?php echo $transaction->updatedAt; ?></span>
                                </div>
                                <div>
                                    <span class="me-1">Date Due:</span>
                                    <span class="fw-medium"><?php echo $transaction->updatedAt; ?></span>
                                </div>
                                <div class="mt-1">
                                    <span class="me-1">Order Status:</span>
                                    <span class="fw-medium"><?php echo $transaction->orderStatus; ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr class="my-0">
                    <div class="card-body">
                        <div class="row p-sm-3 p-0">
                            <div class="col-xl-6 col-md-12 col-sm-5 col-12 mb-xl-0 mb-md-4 mb-sm-0 mb-4">
                                <h6 class="pb-2">Invoice To:</h6>
                                <p class="mb-1"><?php echo $transaction->payer_name . " : " . $transaction->lastName . " " . $transaction->firstName; ?></p>
                                <p class="mb-1"><?php echo $transaction->payer_country; ?></p>
                                <p class="mb-1"><?php echo $transaction->payer_email; ?></p>
                                <p class="mb-1"><?php echo $transaction->phoneNumber; ?></p>
                            </div>
                            <div class="col-xl-6 col-md-12 col-sm-7 col-12">
                                <h6 class="pb-2">Bill To:</h6>
                                <table>
                                    <tbody>
                                        <tr>
                                            <td class="pe-3">Total Due:</td>
                                            <td>$<?php echo $transaction->paid_amount; ?></td>
                                        </tr>
                                        <tr>
                                            <td class="pe-3">Bank name:</td>
                                            <td><?php echo $transaction->payment_source; ?></td>
                                        </tr>
                                        <tr>
                                            <td class="pe-3">Currency:</td>
                                            <td><?php echo $transaction->paid_amount_currency; ?></td>
                                        </tr>
                                        <tr>
                                            <td class="pe-3">Merchant:</td>
                                            <td><?php echo $transaction->merchant_email; ?></td>
                                        </tr>
                                        <tr>
                                            <td class="pe-3">Merchant code:</td>
                                            <td><?php echo $transaction->merchant_id; ?></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table border-top m-0">
                            <thead>
                                <tr>
                                    <th>Item</th>
                                    <th>Description</th>
                                    <th>Price</th>
                                    <th>Qty</th>
                                    <th>Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($orderTransaction as $order) : ?>
                                    <tr>
                                        <td class="text-nowrap image-product">
                                            <img src="<?php echo $order->imageUrl; ?>" alt="order-product">
                                        </td>
                                        <td class="text-nowrap"><?php echo $order->name; ?></td>
                                        <td class="">$<?php echo $order->price ?></td>
                                        <td class=""><?php echo $order->quantity; ?></td>
                                        <td class="">$<?php echo $order->quantity * $order->price; ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <div class="card-body">
                        <div class="row">
                            <div class="col-12">
                                <span class="fw-medium">Note:</span>
                                <span>It was a pleasure working with you and your team. We hope you will keep us in mind for future freelance
                                    projects. Thank You!</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-1"></div>
        </div>
    </div>
</div>

<?php require_once dirname(__DIR__) . "/inc/components/footer.php"; ?>
<script src="<?php echo APP_URL; ?>/js/header/dropdown.js"></script>
<script src="<?php echo APP_URL; ?>/js/header/searchbar.js"></script>