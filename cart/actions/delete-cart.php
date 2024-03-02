<?php

require_once dirname(dirname(__DIR__)) . "/inc/init.php";
require_once dirname(dirname(__DIR__)) . "/inc/utils.php";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && Auth::isLoggedIn()) {

        if (isset($_POST['productId']) && $_POST['action'] === DELETE) {
            if (!isset($conn))
                $conn = require_once dirname(dirname(__DIR__)) . "/inc/db.php";

            $userId = $_SESSION['userId'];
            $productId = $_POST['productId'];

            $deleteResponse = Cart::deleteProductFromCart($conn, $userId, $productId);

            throwStatusMessage($deleteResponse);
        }

        if ($_POST['action'] === DELETE_ALL) {
            if (!isset($conn))
                $conn = require_once dirname(dirname(__DIR__)) . "/inc/db.php";

            $userId = $_SESSION['userId'];
            $deleteAllResponse = Cart::deleteAllProductFromCart($conn, $userId);

            throwStatusMessage($deleteAllResponse);
        }
    }
}
