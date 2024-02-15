<?php

class Order
{
    public $orderId;
    public $productId;
    public $quantity;
    public $price;
    public $createdAt;
    public $updatedAt;

    public function __construct($orderId, $productId, $quantity, $price) {
        $this->orderId = $orderId;
        $this->productId = $productId;
        $this->quantity = $quantity;
        $this->price = $price;
    }

    public static function createOrderByCart($conn, $userId, $cartId) {
        /**
         * Write your code here
         */
    }

    public static function createOrderByProduct($conn, $userId, $productId) {
        /**
         * Write your code here
         */
    }

    public static function updateOrder($conn, $orderId) {
        /**
         * Write your code here
         */
    }

    public static function deleteOrder($conn, $orderId) {
        /**
         * Optional
         * Write your code here
         */
    }

    public static function getOrders($conn, $userId) {
        /**
         * Write your code here
         */
    }

    public static function getOrderById($conn, $orderId) {
        /**
         * Optional
         * Write your code here
         */
    }

    public static function getOrderByUserId($conn, $userId) {
        /**
         * Write your code here
         */
    }
}
