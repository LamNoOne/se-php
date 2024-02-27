<?php

require_once dirname(__DIR__) . "/services/message.php";
require_once dirname(__DIR__) . "/services/validation.php";

class Cart
{
    public $cartId;
    public $productId;
    public $quantity;
    public $createdAt;
    public $updatedAt;

    public function __construct($cartId, $productId, $quantity)
    {
        $this->cartId = $cartId;
        $this->productId = $productId;
        $this->quantity = $quantity;
    }

    private static function createCart($conn)
    {
        /**
         * Write your code here
         * Auto created when creating a new user using procedure
         */
    }

    public static function updateCart($conn, $cartData)
    {
        /**
         * Write your code here
         * Cart Data contains cartId and more information
         */
    }

    public static function getCartByUserId($conn, $userId): array | object
    {
        /**
         * Write your code here
         */

        try {
            $query = 'SELECT * FROM cart WHERE userId=:userId';

            $stmt = $conn->prepare($query);
            $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
            $stmt->setFetchMode(PDO::FETCH_OBJ);
            if (!$stmt->execute())
                throw new Exception('Can not execute query');
            $cart = $stmt->fetch();
            return Message::messageData(true, 'Get cart successfully', $cart);
        } catch (PDOException $e) {
            return Message::message(false, "Can not get cart by userId: " . $e->getMessage());
        }
    }

    public static function getProductCartById($conn, $cartId)
    {
        /**
         * Write your code here
         */
    }

    public static function getProductCartByUserId($conn, $userId)
    {
        /**
         * Write your code here
         */
    }

    public static function addProductToCart($conn, $cartData)
    {
        try {

            // define pattern for cartData, contains key
            $cartDataPattern = ['userId', 'productId', 'quantity'];
            // validate data, array is not empty and contains defined keys
            if (!Validation::validateData($cartDataPattern, $cartData))
                throw new InvalidArgumentException('Invalid cart data');

            // get value from cartData
            $userId = $cartData['userId'];
            $productId = $cartData['productId'];
            $quantity = $cartData['quantity'];

            // get cartId using userId
            $cartData = static::getCartByUserId($conn, $userId);

            // check the returned value
            if (!$cartData['status'] || !is_object($cartData['data']))
                throw new Exception('Cart not found');

            $cartId = $cartData['data']->id;

            // Use cartId from static function getCartByUserId()
            // define the query statement
            $insert = "INSERT INTO cartdetail (cartId, productId, quantity) VALUES (:cartId, :productId, :quantity)";
            $stmt = $conn->prepare($insert);
            // insert the value into cart db
            $status = $stmt->execute(
                [
                    ":cartId" => $cartId,
                    ":productId" => $productId,
                    ":quantity" => $quantity
                ]
            );
            // if not status, throw exception
            if (!$status) throw new InvalidArgumentException('Invalid arguments');
            // return message to display in toast
            return Message::message(true, 'Add product to cart successfully');
        } catch (Exception $e) {
            return Message::message(false, $e->getMessage());
        }
    }


    // remove product from cart

    public static function deleteProductFromCart($conn, $userId, $productId)
    {

        $deleteStmt = "DELETE FROM cartdetail WHERE cartId=:cartId AND productId=:productId";

        try {
            // get cartId using userId
            $cartData = static::getCartByUserId($conn, $userId);

            // check the returned value
            if (!$cartData['status'] || !is_object($cartData['data']))
                throw new Exception('Cart not found');

            $cartId = $cartData['data']->id;

            $stmt = $conn->prepare($deleteStmt);
            $status = $stmt->execute(
                [
                    ":cartId" => $cartId,
                    ":productId" => $productId
                ]
            );
            if (!$status) throw new PDOException('Can not delete product from cart');
            return Message::message(true, 'Delete product from cart successfully');
        } catch (Exception $e) {
            return Message::message(false, $e->getMessage());
        }
    }
}
