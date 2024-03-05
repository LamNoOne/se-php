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

    public static function getOrderByTransaction($conn, $transaction_id)
    {
        try {
            $queryTransaction =
                "SELECT
                    PAY.order_id,
                    PAY.invoice_id,
                    PAY.transaction_id,
                    PAY.payer_id,
                    PAY.payer_name,
                    PAY.payer_email,
                    PAY.payer_country,
                    PAY.merchant_id,
                    PAY.merchant_email,
                    PAY.paid_amount,
                    PAY.paid_amount_currency,
                    PAY.payment_source,
                    PAY.payment_status,
                    O.orderStatusId,
                    OD.name as `orderStatus`,
                    U.lastName,
                    U.firstName,
                    U.email,
                    U.imageUrl,
                    O.shipAddress,
                    O.phoneNumber,
                    PAY.createdAt,
                    PAY.updatedAt
                    FROM 
                    `payment` AS PAY
                    JOIN 
                    `order` AS O ON O.id = PAY.order_id
                    JOIN 
                    `user` AS U ON U.id = O.userId
                    JOIN
                    `orderstatus` as OD ON OD.id = O.orderStatusId
                    WHERE transaction_id = :transaction_id";

            $stmtTransaction = $conn->prepare($queryTransaction);
            $stmtTransaction->bindParam(":transaction_id", $transaction_id, PDO::PARAM_STR);
            $stmtTransaction->setFetchMode(PDO::FETCH_OBJ);

            if (!$stmtTransaction->execute()) {
                throw new Exception('Can not execute query');
            }

            $transaction = $stmtTransaction->fetch();

            $queryOrderTransaction =
                "SELECT
                OD.productId,
                OD.quantity,
                OD.price,
                PRO.name,
                PRO.description,
                PRO.imageUrl
                FROM 
                `payment` AS PAY
                JOIN 
                `orderdetail` AS OD ON OD.orderId = PAY.order_id
                JOIN 
                `product` AS PRO ON PRO.id = OD.productId
                WHERE transaction_id = :transaction_id";

            $stmtOrderTransaction = $conn->prepare($queryOrderTransaction);
            $stmtOrderTransaction->bindParam(":transaction_id", $transaction_id, PDO::PARAM_STR);
            $stmtOrderTransaction->setFetchMode(PDO::FETCH_OBJ);

            if (!$stmtOrderTransaction->execute()) {
                throw new Exception('Can not execute query');
            }
            $orderTransaction = $stmtOrderTransaction->fetchAll();

            return Message::messageData(true, 'Get transaction successfully', ['transaction' => $transaction, 'orderTransaction' => $orderTransaction]);
        } catch (Exception $e) {
            return Message::message(false, $e->getMessage());
        }
    }

    public static function getOrderByUserId($conn, $userId)
    {
        try {
            // get orders
            $queryOrder =
                "SELECT
                O.id,
                O.orderStatusId,
                O.shipAddress,
                O.phoneNumber,
                OT.name
                FROM 
                `order` AS O
                JOIN 
                `user` AS U ON U.id = O.userId
                JOIN
                `orderstatus` AS OT ON OT.id = O.orderStatusId
                WHERE U.id = :userId";

            $stmtOrder = $conn->prepare($queryOrder);
            $stmtOrder->bindParam(":userId", $userId, PDO::PARAM_INT);
            $stmtOrder->setFetchMode(PDO::FETCH_OBJ);
            if (!$stmtOrder->execute()) {
                throw new Exception('Can not execute query');
            }
            $order = $stmtOrder->fetchAll();

            // get order id from order
            // loop through order get order details
            $tmpOrders = array();

            foreach($order as $singleOrder) {
                $orderId = $singleOrder->id;
                $orderDetailData = static::getOrderDetailByUser($conn, $orderId);
                if(!$orderDetailData['status'])
                    throw new Exception("Order detail not found");
                $orderDetail = $orderDetailData['data'];
                $singleOrder->orderDetail = $orderDetail;
                $tmpOrders = [...$tmpOrders, $singleOrder];
            }

            return Message::messageData(true, 'Get order successfully', $tmpOrders);
        } catch (Exception $e) {
            return Message::message(false, $e->getMessage());
        }
    }

    public static function updateOrderStatus($conn, $orderId, $orderStatusId)
    {
        try {
            $updateStmt = "UPDATE `order` SET orderStatusId = :orderStatusId WHERE id = :orderId";
            $stmt = $conn->prepare($updateStmt);
            // bind the update statement
            $stmt->bindParam(":orderId", $orderId, PDO::PARAM_INT);
            $stmt->bindParam(":orderStatusId", $orderStatusId, PDO::PARAM_INT);
            $status = $stmt->execute();
            if (!$status) {
                throw new PDOException("Can not execute query");
            }
            return Message::message(true, "Update order status successfully");
        } catch (Exception $e) {
            return Message::message(false, $e->getMessage());
        }
    }

    public static function getOrderDetailByUser($conn, $orderId)
    {
        try {
            // get order details
            $queryOrderDetail =
                "SELECT
                    OD.productId,
                    OD.quantity,
                    OD.price,
                    OT.id as `orderStatusId`,
                    OT.name as `orderStatus`,
                    P.name,
                    P.description,
                    P.imageUrl
                    FROM 
                    `order` AS O
                    JOIN
                    `orderstatus` AS OT ON OT.id = O.orderStatusId
                    JOIN 
                    `orderdetail` AS OD ON OD.orderId = O.id
                    JOIN
                    `product` AS P ON P.id = OD.productId
                    WHERE O.id = :orderId";
            $stmtOrderDetail = $conn->prepare($queryOrderDetail);
            $stmtOrderDetail->bindParam(":orderId", $orderId, PDO::PARAM_INT);

            $stmtOrderDetail->setFetchMode(PDO::FETCH_OBJ);
            if (!$stmtOrderDetail->execute()) {
                throw new Exception('Can not execute query');
            }
            $orderDetail = $stmtOrderDetail->fetchAll();
            return Message::messageData(true, 'Get orderDetail successfully', $orderDetail);
        } catch (Exception $e) {
            return Message::message(false, $e->getMessage());
        }
    }
}
