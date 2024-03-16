<?php 

require_once dirname(dirname(__DIR__)) . "/inc/init.php";
require_once dirname(dirname(__DIR__)). "/inc/utils.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && Auth::isLoggedIn()) {
    if(isset($_POST['orderId'])) {
        $conn = require_once dirname(dirname(__DIR__)) . "/inc/db.php";
        $response = Order::cancelOrder($conn, $_POST['orderId']);
        return throwStatusMessage($response);
    }
    return throwStatusMessage(['status' => false, 'message' => "Invalid Order"]);
}