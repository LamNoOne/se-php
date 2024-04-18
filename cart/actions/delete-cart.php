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
            $product = Cart::getProductDetailFromCart($conn, $userId, $productId)['data'];
            $singleProductQuantity = $product->quantity;
            $singleProductStockQuantity = $product->stockQuantity;
            $newStockQuantity = $singleProductStockQuantity + $singleProductQuantity;

            $deleteResponse = Cart::deleteProductFromCart($conn, $userId, $productId);
            if ($deleteResponse['status']) {
                Product::updateStockQuantity($conn, $productId, $newStockQuantity);
            }

            throwStatusMessage($deleteResponse);
        }

        if ($_POST['action'] === DELETE_ALL) {
            if (!isset($conn))
                $conn = require_once dirname(dirname(__DIR__)) . "/inc/db.php";

            $userId = $_SESSION['userId'];

            $allProductCart = Cart::getAllProductFromCart($conn, $userId)['data'];
            $deleteAllResponse = Cart::deleteAllProductFromCart($conn, $userId);

            if($deleteAllResponse['status']) {
                foreach ($allProductCart as $product) {
                    $singleProductQuantity = $product->quantity;
                    $singleProductStockQuantity = $product->stockQuantity;
                    $newStockQuantity = $singleProductStockQuantity + $singleProductQuantity;
                    Product::updateStockQuantity($conn, $product->productId, $newStockQuantity);
                }
            }

            throwStatusMessage($deleteAllResponse);
        }
    }
}
