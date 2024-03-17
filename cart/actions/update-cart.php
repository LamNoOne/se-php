<?php

require_once dirname(dirname(__DIR__)) . "/inc/init.php";
require_once dirname(dirname(__DIR__)) . "/inc/utils.php";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['productId']) && isset($_POST['productQuantity']) && Auth::isLoggedIn()) {
        if (!isset($conn))
            $conn = require_once dirname(dirname(__DIR__)) . "/inc/db.php";

        $productId = $_POST['productId'];
        $productQuantity = $_POST['productQuantity'];
        $userId = $_SESSION['userId'];

        $cartData = [
            'productId' => intval($productId),
            'quantity' => intval($productQuantity),
        ];

        $product = Cart::getProductDetailFromCart($conn, $userId, $productId)['data'];

        $cartResponse = Cart::updateCart($conn, $userId, $cartData);
        if ($cartResponse['status']) {

            $tempStockQuantity = $productQuantity - $product->quantity;
            if ($tempStockQuantity > 0) {
                $newStockQuantity = $product->stockQuantity - $tempStockQuantity;
            } else {
                $newStockQuantity = $product->stockQuantity + abs($tempStockQuantity);
            }
            Product::updateStockQuantity($conn, $productId, $newStockQuantity);
        }

        throwStatusMessage($cartResponse);
    }
}
