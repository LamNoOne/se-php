<?php

session_start();

require_once(dirname(dirname(__DIR__)) . '/inc/utils.php');

require_once(dirname(dirname(__DIR__)) . '/classes/controllers/cart.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($_POST['productId']) {
        if (!isset($conn))
            $conn = require_once(dirname(dirname(__DIR__)) . '/inc/db.php');
        // If user is logged in
        $userId = $_POST['userId'];
        // Get product information from submit
        $productId = $_POST['productId'];
        $quantity = $_POST['quantity'];

        $cartData = [
            'productId' => $productId,
            'quantity' => $quantity
        ];
        $message = Cart::addProductToCart($conn, $userId, $cartData);
        if($message['status']) {
            $product = Product::getProductById($conn, $productId);
            $newStockQuantity = $product->stockQuantity - $quantity;
            Product::updateStockQuantity($conn, $productId, $newStockQuantity);
        }
        throwStatusMessage($message);
    }
}
