<?php

require_once dirname(dirname(__DIR__)) . "/inc/init.php";
require_once dirname(dirname(__DIR__)) . "/inc/utils.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && Auth::isLoggedIn()) {
    if (isset($_POST['orderId'])) {
        $conn = require_once dirname(dirname(__DIR__)) . "/inc/db.php";

        $allOrderProduct = Order::getOrderDetailByUser($conn, $_POST['orderId'])['data'];
        foreach ($allOrderProduct as $product) {
            $productId = $product->productId;
            $quantity = $product->quantity;
            if (Product::isProductOutOfStock($conn, $productId, $quantity)) {
                return throwStatusMessage(['status' => false, 'message' => "Some products out of stock"]);
            }
        }

        $response = Cart::createCartFromOrder($conn, $_POST['orderId']);
        if($response['status']) {
            foreach ($allOrderProduct as $product) {
                $newStockQuantity = $product->stockQuantity - $product->quantity;
                Product::updateStockQuantity($conn, $product->productId, $newStockQuantity);
            }
        }
        return throwStatusMessage($response);
    }
    return throwStatusMessage(['status' => false, 'message' => "Invalid Order"]);
}

//getOrderDetailByUser