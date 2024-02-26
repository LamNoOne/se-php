<?php

require_once dirname(__DIR__) . "/services/message.php";

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

    public static function deleteProductFromCart($conn, $cartId)
    {
        /**
         * Write your code here
         */
    }

    public static function getCartByUserId($conn, $userId) : array | object
    {
        /**
         * Write your code here
         */

        try {
            $query = 'SELECT * FROM cart WHERE userId=:userId';

            $stmt = $conn->prepare($query);
            $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
            $stmt->setFetchMode(PDO::FETCH_OBJ);
            if(!$stmt->execute())
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

    public static function addProductToCart($conn, $userId, $productId)
    {
        try {

            $cartData = static::getCartByUserId($conn, $userId);

            if(!$cartData['status'] || !is_object($cartData['data']))
                throw new Exception('Cart not found');
            
            $cartId = $cartData['data']->id;

            // Use cartId from static function getCartByUserId()
            $insert = "INSERT INTO cartdetail (cartId, productId) VALUES (:cartId, :productId)";
            $stmt = $conn->prepare($insert);
            $status = $stmt->execute(
                [
                    ":cartId" => $cartId,
                    ":productId" => $productId
                ]
            );
            if (!$status) throw new InvalidArgumentException('Invalid arguments');
            return Message::message(true, 'Add product to cart successfully');
        } catch (Exception $e) {
            return Message::message(false, $e->getMessage());
        }
    }
}
