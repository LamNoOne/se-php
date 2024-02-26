<?php 

session_start();

require_once(dirname(dirname(__DIR__)) . '/inc/utils.php');

require_once (dirname(dirname(__DIR__)) . '/classes/controllers/cart.php');

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    if($_POST['productId']) {
        $conn = require_once(dirname(dirname(__DIR__)) . '/inc/db.php');
        $userId = $_SESSION['userId'];
        
        $productId = $_POST['productId'];
        $message = Cart::addProductToCart($conn, $userId, $productId);
        throwStatusMessage($message);
    }
}