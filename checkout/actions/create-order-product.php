<?php

require_once dirname(dirname(__DIR__)) . "/inc/init.php";
require_once dirname(dirname(__DIR__)) . "/inc/utils.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && Auth::isLoggedIn()) {
    if(!isset($conn))
        $conn = require_once dirname(dirname(__DIR__)). "/inc/db.php";
    
    $userId = $_SESSION['userId'];
    $productCheckout = $_POST['productCheckout'];
    $productId = $productCheckout['productId'];
    $productQuantity = $productCheckout['productQuantity'];
    $address = $_POST['address'];
    $phone = $_POST['phone'];


    $data = [
        'userId' => $userId,
        'productId' => $productId,
        'quantity' => $productQuantity,
        'shipAddress' => $address,
        'phoneNumber' => $phone
    ];

    $response = Order::createOrderByProduct($conn, $data);
    if($response['status']) {
        $product = Product::getProductById($conn, $productId);
        $newProductStock = $product->stockQuantity - $productQuantity;
        Product::updateStockQuantity($conn, $productId, $newProductStock);
    }
    throwStatusMessage($response);
}
