<?php

class Cart
{
    public $cartId;
    public $productId;
    public $quantity;
    public $createdAt;
    public $updatedAt;

    public function __construct($cartId, $productId, $quantity) {
        $this->cartId = $cartId;
        $this->productId = $productId;
        $this->quantity = $quantity;
    }

    private static function createCart($conn) {
        /**
         * Write your code here
         * Auto created when creating a new user using procedure
         */
    }

    public static function updateCart($conn, $cartData) {
        /**
         * Write your code here
         * Cart Data contains cartId and more information
         */
    }

    public static function deleteProductFromCart($conn, $cartId) {
        /**
         * Write your code here
         */
    }

    public static function getCarts($conn, $userId) {
        /**
         * Write your code here
         */
    }

    public static function getProductCartById($conn, $cartId) {
        /**
         * Write your code here
         */
    }

    public static function getProductCartByUserId($conn, $userId) {
        /**
         * Write your code here
         */
    }
}
