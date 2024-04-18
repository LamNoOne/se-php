<?php

require_once dirname(dirname(__DIR__)) . "/inc/init.php";
require_once dirname(dirname(__DIR__)) . "/inc/utils.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && Auth::isLoggedIn()) {
    if (isset($_POST['orderId'])) {
        $conn = require_once dirname(dirname(__DIR__)) . "/inc/db.php";
        $allOrderDetail = Order::getOrderDetailByUser($conn, $_POST['orderId'])['data'];
        $response = Order::cancelOrder($conn, $_POST['orderId']);
        if ($response['status']) {
            foreach ($allOrderDetail as $orderDetail) {
                if (intval($orderDetail->orderStatusId) === PENDING) {
                    $productId = $orderDetail->productId;
                    $newStockQuantity = $orderDetail->stockQuantity + $orderDetail->quantity;
                    Product::updateStockQuantity($conn, $productId, $newStockQuantity);
                }
            }
        }
        return throwStatusMessage($response);
    }
    return throwStatusMessage(['status' => false, 'message' => "Invalid Order"]);
}
