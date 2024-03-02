<?php
require_once dirname(__DIR__) . "/services/message.php";
require_once dirname(__DIR__) . "/services/validation.php";
class Order
{
    public $orderId;
    public $productId;
    public $quantity;
    public $price;
    public $createdAt;
    public $updatedAt;

    public function __construct($orderId, $productId, $quantity, $price)
    {
        $this->orderId = $orderId;
        $this->productId = $productId;
        $this->quantity = $quantity;
        $this->price = $price;
    }

    public static function createOrderByCart($conn, $data)
    {
        $createOrderStatement =
            "CALL createOrderSelectedProductCart(:userId, :productsId, :shipAddress, :phoneNumber, @p_orderId, @p_errorMessage)";
        $createOrderPattern = ['userId', 'productsId', 'shipAddress', 'phoneNumber'];

        try {
            // validate orders data
            if (!Validation::validateData($createOrderPattern, $data)) {
                throw new InvalidArgumentException('Invalid order data');
            }

            // convert array to string
            $productList = implode(",", array_map(fn ($item) => (int)$item, $data['productsId']));
            $stmt = $conn->prepare($createOrderStatement);

            $stmt->bindParam(":userId", $data['userId'], PDO::PARAM_INT);
            $stmt->bindParam(":productsId", $productList, PDO::PARAM_STR);
            $stmt->bindParam(":shipAddress", $data['shipAddress'], PDO::PARAM_STR);
            $stmt->bindParam(":phoneNumber", $data['phoneNumber'], PDO::PARAM_STR);

            $status = $stmt->execute();
            if (!$status) {
                throw new PDOException("Can not execute query");
            }

            $stmt = $conn->query("SELECT @p_orderId as orderId, @p_errorMessage as errorMessage");
            $result = $stmt->fetch(PDO::FETCH_OBJ);
            return Message::messageData(true, "Create order successfully", $result);
        } catch (Exception $e) {
            return Message::message(false, $e->getMessage());
        }
    }

    public static function createOrderByProduct($conn, $userId, $productId)
    {
        /**
         * Write your code here
         */
    }

    public static function updateOrder($conn, $orderId)
    {
        /**
         * Write your code here
         */
    }

    public static function deleteOrder($conn, $orderId)
    {
        /**
         * Optional
         * Write your code here
         */
    }

    public static function getOrders($conn, $userId)
    {
        /**
         * Write your code here
         */
    }

    public static function getOrderById($conn, $orderId)
    {
        try {
            $queryOrderDetail = 
                    "SELECT 
                        `orderdetail`.`orderId`,
                        `orderdetail`.`productId`, 
                        `orderdetail`.`quantity`, 
                        `orderdetail`.`price`,
                        `product`.`id`,
                        `product`.`name`,
                        `product`.`description`,
                        `product`.`imageUrl`,
                        `product`.`screen`,
                        `product`.`operatingSystem`,
                        `product`.`processor`,
                        `product`.`ram`,
                        `product`.`storageCapacity`,
                        `product`.`weight`,
                        `product`.`batteryCapacity`,
                        `product`.`color`
                    FROM `orderdetail` JOIN `product`
                    ON `orderdetail`.`productId` = `product`.`id`
                    WHERE `orderdetail`.`orderId` = :orderId";

                $queryOrder = 
                    "SELECT
                        `user`.`firstName`,
                        `user`.`lastName`,
                        `order`.`shipAddress`, 
                        `order`.`phoneNumber`,
                        `order`.`orderStatusId`,
                        `orderstatus`.`name`
                    FROM `order` 
                    JOIN `orderstatus` ON `order`.`orderStatusId` = `orderstatus`.`id`
                    JOIN `user` ON `order`.`userId` = `user`.`id`
                    WHERE `order`.`id` = :orderId";

                $stmtOrderDetail = $conn->prepare($queryOrderDetail);
                $stmtOrderDetail->bindParam(":orderId", $orderId, PDO::PARAM_INT);
                $stmtOrderDetail->setFetchMode(PDO::FETCH_OBJ);

                if (!$stmtOrderDetail->execute()) {
                    throw new Exception('Can not execute query');
                }
                $orderDetail = $stmtOrderDetail->fetchAll();

                $stmtOrder = $conn->prepare($queryOrder);
                $stmtOrder->bindParam(":orderId", $orderId, PDO::PARAM_INT);
                $stmtOrder->setFetchMode(PDO::FETCH_OBJ);

                if (!$stmtOrder->execute()) {
                    throw new Exception('Can not execute query');
                }
                $order = $stmtOrder->fetch();

                return Message::messageData(true, 'Get orderDetail successfully', ['order' => $order, 'orderDetail' => $orderDetail]);
        } catch (Exception $e) {
            return Message::message(false, $e->getMessage());
        }
    }

    public static function getOrderByUserId($conn, $userId)
    {
        /**
         * Write your code here
         */
    }
}
