<?php

require_once dirname(dirname(__DIR__)) . "/inc/init.php";
require_once dirname(dirname(__DIR__)) . "/inc/utils.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && Auth::isLoggedIn()) {
    if(!isset($conn))
        $conn = require_once dirname(dirname(__DIR__)). "/inc/db.php";
    
    $userId = $_SESSION['userId'];
    $productList = $_POST['productList'];
    $address = $_POST['address'];
    $phone = $_POST['phone'];


    $data = [
        'userId' => $userId,
        'productsId' => $productList,
        'shipAddress' => $address,
        'phoneNumber' => $phone
    ];

    $response = Order::createOrderByCart($conn, $data);
    throwStatusMessage($response);
}
