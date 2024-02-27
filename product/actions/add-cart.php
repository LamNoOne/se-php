<?php 

session_start();

require_once(dirname(dirname(__DIR__)) . '/inc/utils.php');

require_once (dirname(dirname(__DIR__)) . '/classes/controllers/cart.php');

if($_SERVER['REQUEST_METHOD'] === 'POST'  && isset($_SESSION['userId'])) {
    if($_POST['productId']) {
        $conn = require_once(dirname(dirname(__DIR__)) . '/inc/db.php');
        // If user is logged in
        $userId = $_SESSION['userId'];
        // Get product information from submit
        $productId = $_POST['productId'];
        $quantity = $_POST['quantity'];

        $cartData = [
            'userId' => $userId,
            'productId' => $productId,
            'quantity' => $quantity
        ];
        $message = Cart::addProductToCart($conn, $cartData);
        throwStatusMessage($message);
    }
}