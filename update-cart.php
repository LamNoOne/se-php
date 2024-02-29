<?php

require_once "inc/init.php";
require_once "inc/utils.php";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['productId']) && isset($_POST['productQuantity']) && Auth::isLoggedIn()) {
        if (!isset($conn))
            $conn = require_once "inc/db.php";

        $productId = $_POST['productId'];
        $productQuantity = $_POST['productQuantity'];
        $userId = $_SESSION['userId'];

        $cartData = [
            'productId' => intval($productId),
            'quantity' => intval($productQuantity),
        ];


        $cartResponse = Cart::updateCart($conn, $userId, $cartData);

        throwStatusMessage($cartResponse);
    }
}
