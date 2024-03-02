<?php
require_once dirname(__DIR__) . "/inc/init.php";
require_once dirname(__DIR__) . "/inc/utils.php";

$payment_ref_id = $statusMsg = '';
$status = 'error';

// Check whether the payment ID is not empty 
if (!empty($_GET['checkout_ref_id'])) {
    $payment_txn_id  = base64_decode($_GET['checkout_ref_id']);
    
    // Establish a PDO connection
    $conn = require_once dirname(__DIR__) . "/inc/db.php";


    // Fetch transaction data from the database 
    $sqlQ = "SELECT * FROM payment WHERE transaction_id = ?";
    $stmt = $conn->prepare($sqlQ);
    $stmt->execute([$payment_txn_id]);

    if ($stmt->rowCount() > 0) {
        // Get transaction details 
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        extract($row);

        $status = 'success';
        $statusMsg = 'Your Payment has been Successful!';
    } else {
        $statusMsg = "Transaction has been failed!";
    }
} else {
    header("Location: index.php");
    exit;
}
?>

<?php if (!empty($id)) : ?>
    <h1 class="<?php echo $status; ?>"><?php echo $statusMsg; ?></h1>

    <h4>Payment Information</h4>
    <p><b>Reference Number:</b> <?php echo $id; ?></p>
    <p><b>Order ID:</b> <?php echo $order_id; ?></p>
    <p><b>Transaction ID:</b> <?php echo $transaction_id; ?></p>
    <p><b>Paid Amount:</b> <?php echo $paid_amount . ' ' . $paid_amount_currency; ?></p>
    <p><b>Payment Status:</b> <?php echo $payment_status; ?></p>
    <p><b>Date:</b> <?php echo $createAt; ?></p>

    <h4>Payer Information</h4>
    <p><b>ID:</b> <?php echo $payer_id; ?></p>
    <p><b>Name:</b> <?php echo $payer_name; ?></p>
    <p><b>Email:</b> <?php echo $payer_email; ?></p>
    <p><b>Country:</b> <?php echo $payer_country; ?></p>

    <h4>Product Information</h4>
    <!-- Assuming you have $itemName and $currency variables defined somewhere -->
    <!-- <p><b>Name:</b> <?php echo $itemName; ?></p>
    <p><b>Price:</b> <?php echo $itemPrice . ' ' . $currency; ?></p> -->
<?php else : ?>
    <h1 class="error">Your Payment has been failed!</h1>
    <p class="error"><?php echo $statusMsg; ?></p>
<?php endif; ?>