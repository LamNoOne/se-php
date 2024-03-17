<?php

require_once dirname(__DIR__) . "/services/message.php";
require_once dirname(__DIR__) . "/services/validation.php";
require_once "product.php";

class Cart
{
    public $cartId;
    public $productId;
    public $quantity;
    public $createdAt;
    public $updatedAt;

    public function __construct($cartId, $productId, $quantity, $createdAt = null, $updatedAt = null)
    {
        $this->cartId = $cartId;
        $this->productId = $productId;
        $this->quantity = $quantity;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
    }

    private static function createCart($conn)
    {
        /**
         * Write your code here
         * Auto created when creating a new user using procedure
         */
    }

    private static function getCartId($conn, $userId)
    {
        // get cartId using userId
        $cartUser = static::getCartByUserId($conn, $userId);
        // check the returned value
        if (!$cartUser['status'] || !is_object($cartUser['data'])) {
            throw new Exception('Cart not found');
        }
        $cartId = $cartUser['data']->id;
        return $cartId;
    }

    public static function createCartFromOrder($conn, $orderId)
    {
        try {
            $createOrderStatement = "CALL createCartFromOrder(:p_orderId, @p_cartId, @p_errorMessage)";
            $stmt = $conn->prepare($createOrderStatement);
            $stmt->bindValue(':p_orderId', $orderId, PDO::PARAM_INT);
            $status = $stmt->execute();
            if (!$status) {
                throw new PDOException("Can not execute query");
            }

            $stmt = $conn->query("SELECT @p_cartId as cartId, @p_errorMessage as errorMessage");
            $result = $stmt->fetch(PDO::FETCH_OBJ);
            return Message::messageData(true, "Create cart successfully", $result);
        } catch (Exception $e) {
            return Message::message(false, $e->getMessage());
        }
    }

    public static function updateCart($conn, $userId, $cartData)
    {
        try {
            $cartId = static::getCartId($conn, $userId);
            // validate cartData
            // define pattern for cartData, contains key
            $cartDataPattern = ['productId', 'quantity'];

            // validate data, array is not empty and contains defined keys
            if (!Validation::validateData($cartDataPattern, $cartData)) {
                throw new InvalidArgumentException('Invalid cart data');
            }

            $product = Product::getProductById($conn, $cartData['productId']);

            if(empty($product)) {
                return Message::message(false, 'Product not found');
            }

            if($product->stockQuantity < $cartData['quantity']) {
                return Message::message(false, 'Product quantity is not enough');
            }

            $updateStatement = "UPDATE cartdetail SET quantity=:quantity WHERE cartId=:cartId AND productId=:productId";
            $stmt = $conn->prepare($updateStatement);
            $status = $stmt->execute(
                [
                    ":cartId" => $cartId,
                    ":productId" => $cartData['productId'],
                    ":quantity" => $cartData['quantity']
                ]
            );
            if (!$status) {
                throw new PDOException("Can not execute query");
            }
            return Message::message(true, "Update product in cart successfully");
        } catch (Exception $e) {
            return Message::message(false, $e->getMessage());
        }
    }

    public static function getCartByUserId($conn, $userId)
    {
        try {
            $query = 'SELECT * FROM cart WHERE userId=:userId';

            $stmt = $conn->prepare($query);
            $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
            $stmt->setFetchMode(PDO::FETCH_OBJ);
            if (!$stmt->execute()) {
                throw new Exception('Can not execute query');
            }
            $cart = $stmt->fetch();
            return Message::messageData(true, 'Get cart successfully', $cart);
        } catch (PDOException $e) {
            return Message::message(false, "Can not get cart by userId: " . $e->getMessage());
        }
    }

    public static function getProductCartById($conn, $cartId, $productId)
    {
        try {
            //code...
            $query = "SELECT * FROM cartdetail WHERE cartId=:cartId AND productId=:productId";
            $stmt = $conn->prepare($query);
            $stmt->bindValue(':cartId', $cartId, PDO::PARAM_INT);
            $stmt->bindValue(':productId', $productId, PDO::PARAM_INT);
            $stmt->setFetchMode(PDO::FETCH_OBJ);
            if (!$stmt->execute()) {
                throw new Exception('Can not execute query');
            }
            $cart = $stmt->fetch();
            return Message::messageData(true, 'Get a product in cart successfully', $cart);
        } catch (Exception $e) {
            return Message::message(false, "Cannot get a product from cart" . $e->getMessage());
        }
    }

    public static function getCartDetailByUserId($conn, $userId)
    {
        try {
            $cartId = static::getCartId($conn, $userId);

            // Use cartId from static function getCartByUserId() ...
            $query = 'SELECT * FROM cartdetail WHERE cartId=:cartId';
            $stmt = $conn->prepare($query);
            $stmt->bindValue(':cartId', $cartId, PDO::PARAM_INT);
            $stmt->setFetchMode(PDO::FETCH_ASSOC);

            if (!$stmt->execute()) {
                throw new PDOException('Can not execute query');
            }

            $cartDetail = $stmt->fetchAll();
            return Message::messageData(true, 'Get cart detail successfully', $cartDetail);
        } catch (Exception $e) {
            return Message::message(false, $e->getMessage());
        }
    }

    public static function getAllProductFromCart($conn, $userId)
    {
        try {
            $cartId = static::getCartId($conn, $userId);

            // define query string
            $query = "SELECT CD.productId, P.name, P.price, P.imageUrl, P.description, P.stockQuantity, CD.quantity FROM cartdetail AS CD JOIN product AS P ON CD.productId = P.id WHERE cartId = :cartId";

            $stmt = $conn->prepare($query);
            $stmt->bindValue(':cartId', $cartId, PDO::PARAM_INT);
            $stmt->setFetchMode(PDO::FETCH_OBJ);
            if (!$stmt->execute()) {
                throw new Exception('Can not execute query');
            }
            $cartDetail = $stmt->fetchAll();
            return Message::messageData(true, 'Get cart detail successfully', $cartDetail);
        } catch (Exception $e) {
            return Message::message(false, $e->getMessage());
        }
    }

    public static function getProductDetailFromCart($conn, $userId, $productId)
    {
        try {
            $cartId = static::getCartId($conn, $userId);

            // define query string
            $query = "SELECT CD.productId, P.name, P.price, P.imageUrl, P.description, P.stockQuantity, CD.quantity FROM cartdetail AS CD JOIN product AS P ON CD.productId = P.id WHERE cartId = :cartId AND productId = :productId";

            $stmt = $conn->prepare($query);
            $stmt->bindValue(':cartId', $cartId, PDO::PARAM_INT);
            $stmt->bindValue(':productId', $productId, PDO::PARAM_INT);
            $stmt->setFetchMode(PDO::FETCH_OBJ);
            if (!$stmt->execute()) {
                throw new Exception('Can not execute query');
            }
            $cartDetail = $stmt->fetch();
            return Message::messageData(true, 'Get a product cart detail successfully', $cartDetail);
        } catch (Exception $e) {
            return Message::message(false, $e->getMessage());
        }
    }

    public static function addProductToCart($conn, $userId, $cartData)
    {

        try {
            // define pattern for cartData, contains key
            $cartDataPattern = ['productId', 'quantity'];
            // validate data, array is not empty and contains defined keys
            if (!Validation::validateData($cartDataPattern, $cartData)) {
                throw new InvalidArgumentException('Invalid cart data');
            }

            // get value from cartData
            $productId = $cartData['productId'];
            $quantity = $cartData['quantity'];

            $product = Product::getProductById($conn, $productId);
            if(empty($product)) {
                return Message::message(false, 'Product not found');
            }

            if($product->stockQuantity < $quantity) {
                return Message::message(false, 'Product quantity is not enough');
            }

            $cartId = static::getCartId($conn, $userId);
            // check if product is already in cart
            $productCartData = static::getProductCartById($conn, $cartId, $productId);
            if ($productCartData['status'] && isset($productCartData['data']) && !empty($productCartData['data'])) {
                // increase product cart quantity and call update cart
                // $cartUpdate = [...$cartData, 'quantity' => $productCartData['data']->quantity + $quantity];
                if($productCartData['data']->quantity + $quantity > $product->stockQuantity) {
                    return Message::message(false, 'Product quantity is not enough');
                }
                $cartUpdate = array_merge($cartData, ['quantity' => $productCartData['data']->quantity + $quantity]);
                $status = static::updateCart($conn, $userId, $cartUpdate)['status'];
                if ($status) {
                    return Message::messageData(true, 'Product exists in cart, update quantity successfully', ['modified' => true]);
                } else {
                    return Message::message(false, 'Update product cart failed');
                }
            }


            // if product is not yet in cart, add product to cart
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
            if (!$status) {
                throw new InvalidArgumentException('Invalid arguments');
            }
            // return message to display in toast
            return Message::message(true, 'Add product to cart successfully');
        } catch (Exception $e) {
            return Message::message(false, $e->getMessage());
        }
    }

    public static function deleteProductFromCart($conn, $userId, $productId)
    {
        try {
            $cartId = static::getCartId($conn, $userId);

            $deleteStmt = "DELETE FROM cartdetail WHERE cartId=:cartId AND productId=:productId";
            $stmt = $conn->prepare($deleteStmt);
            $status = $stmt->execute(
                [
                    ":cartId" => $cartId,
                    ":productId" => $productId
                ]
            );
            if (!$status) {
                throw new PDOException('Can not delete product from cart');
            }
            return Message::message(true, 'Delete product from cart successfully');
        } catch (Exception $e) {
            return Message::message(false, $e->getMessage());
        }
    }

    public static function deleteAllProductFromCart($conn, $userId)
    {
        try {
            $cartId = static::getCartId($conn, $userId);
            $deleteStmt = "DELETE FROM cartdetail WHERE cartId=:cartId";
            $stmt = $conn->prepare($deleteStmt);
            $status = $stmt->execute([":cartId" => $cartId]);
            if (!$status) {
                throw new PDOException('Can not delete product from cart');
            }
            return Message::message(true, 'Delete all products from cart successfully');
        } catch (Exception $e) {
            return Message::message(false, $e->getMessage());
        }
    }
}
