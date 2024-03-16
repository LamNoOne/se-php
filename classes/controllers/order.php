<?php
require_once dirname(__DIR__) . "/services/message.php";
require_once dirname(__DIR__) . "/services/validation.php";
require_once dirname(__DIR__) . "/services/datafetcher.php";
require_once dirname(dirname(__DIR__)) . "/inc/utils.php";

class Order extends DataFetcher
{
    public $orderId;
    public $productId;
    public $quantity;
    public $price;
    public $createdAt;
    public $updatedAt;

    public function __construct($orderId = null, $productId = null, $quantity = null, $price = null)
    {
        $this->orderId = $orderId;
        $this->productId = $productId;
        $this->quantity = $quantity;
        $this->price = $price;
    }

    public static function cancelOrder($conn, $orderId)
    {
        try {
            $queryOrderStatus = "SELECT orderStatusId FROM `order` WHERE id = :orderId";
            $stmtOrderStatus = $conn->prepare($queryOrderStatus);
            $stmtOrderStatus->bindParam(":orderId", $orderId, PDO::PARAM_INT);
            $stmtOrderStatus->setFetchMode(PDO::FETCH_OBJ);
            if (!$stmtOrderStatus->execute()) {
                throw new PDOException('Cannot execute query');
            }
            $orderStatus = $stmtOrderStatus->fetch();
            if (intval($orderStatus->orderStatusId) === PENDING) {
                return static::updateOrderStatus($conn, $orderId, CANCELLED);
            }
            return static::updateOrderStatus($conn, $orderId, PENDING_CANCEL);
        } catch (Exception $e) {
            return Message::message(false, 'Something went wrong');
        }
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

    public static function createOrderByProduct($conn, $data)
    {
        $createOrderStatement =
            "CALL createOrderDirectlyProduct(:userId, :productId, :quantity, :shipAddress, :phoneNumber, @p_orderId, @p_errorMessage)";
        $createOrderPattern = ['userId', 'productId', 'quantity', 'shipAddress', 'phoneNumber'];

        try {
            // validate orders data
            if (!Validation::validateData($createOrderPattern, $data)) {
                throw new InvalidArgumentException('Invalid order data');
            }

            $stmt = $conn->prepare($createOrderStatement);

            $stmt->bindParam(":userId", $data['userId'], PDO::PARAM_INT);
            $stmt->bindParam(":productId", $data['productId'], PDO::PARAM_INT);
            $stmt->bindParam(":quantity", $data['quantity'], PDO::PARAM_INT);
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

    public static function getOrderByUserId($conn, $userId, $limit = 20, $offset = 0)
    {
        try {
            $queryOrder = [
                'fields' => 'O.id, P.transaction_id, O.orderStatusId, O.shipAddress, O.phoneNumber, OS.name AS status, O.createdAt, O.updatedAt',
                'joins' => [
                    ['type' => 'INNER', 'table' => '`user` AS U', 'on' => 'U.id = O.userId'],
                    ['type' => 'INNER', 'table' => '`orderstatus` AS OS', 'on' => 'OS.id = O.orderStatusId'],
                    ['type' => 'LEFT', 'table' => '`payment` AS P', 'on' => 'P.order_id = O.id'],
                ],
                'filters' => [
                    ['column' => 'U.id', 'operator' => NULL, 'alias' => 'userId', 'value' => $userId]
                ],
                'orderBy' => 'O.createdAt DESC',
                'limit' => $limit,
                'offset' => $offset,
            ];

            $dataFetcher = new DataFetcher($conn);
            $orders = $dataFetcher->fetchData("`order` AS O", $queryOrder);
            // get order id from order
            // loop through order get order details
            $_tmpOrders = [...$orders['data']];
            $_orders = array();

            foreach ($_tmpOrders as $singleOrder) {
                $orderId = $singleOrder->id;
                $orderDetailData = static::getOrderDetailByUser($conn, $orderId);
                if (!$orderDetailData['status'])
                    throw new Exception("Order detail not found");
                $orderDetail = $orderDetailData['data'];
                $singleOrder->orderDetail = $orderDetail;
                $_orders = [...$_orders, $singleOrder];
            }

            // $orders = [...$orders, 'data' => $_orders];
            $orders = array_merge($orders, ['data' => $_orders]);

            return $orders;
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
                    P.imageUrl,
                    OD.createdAt,
                    OD.updatedAt
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

    public static function getOrderByIdV2($conn, $id)
    {
        try {
            $projection =  [
                [
                    'table' => TABLES['ORDER'],
                    'column' => 'id'
                ],
                [
                    'table' => TABLES['USER'],
                    'column' => 'id',
                    'as' => 'customerId'
                ],
                [
                    'table' => TABLES['USER'],
                    'column' => 'firstName',
                    'as' => 'customerFirstName'
                ],
                [
                    'table' => TABLES['USER'],
                    'column' => 'lastName',
                    'as' => 'customerLastName'
                ],
                [
                    'table' => TABLES['USER'],
                    'column' => 'imageUrl',
                    'as' => 'customerImageUrl'
                ],
                [
                    'table' => TABLES['USER'],
                    'column' => 'email',
                    'as' => 'customerEmail'
                ],
                [
                    'table' => TABLES['USER'],
                    'column' => 'phoneNumber',
                    'as' => 'customerPhoneNumber'
                ],
                [
                    'table' => TABLES['USER'],
                    'column' => 'address',
                    'as' => 'customerAddress'
                ],
                [
                    'table' => TABLES['ORDER'],
                    'column' => 'shipAddress'
                ],
                [
                    'table' => TABLES['ORDER'],
                    'column' => 'phoneNumber'
                ],
                [
                    'table' => TABLES['ORDER_STATUS'],
                    'column' => 'id',
                    'as' => 'statusId'
                ],
                [
                    'table' => TABLES['ORDER_STATUS'],
                    'column' => 'name',
                    'as' => 'statusName'
                ],
                [
                    'table' => TABLES['ORDER'],
                    'column' => 'createdAt'
                ],
                [
                    'table' => TABLES['ORDER'],
                    'column' => 'updatedAt'
                ]
            ];
            $join =  [
                'tables' => [
                    TABLES['ORDER'],
                    TABLES['ORDER_STATUS'],
                    TABLES['USER'],
                ],
                'on' => [
                    [
                        'table1' => TABLES['ORDER'],
                        'table2' => TABLES['ORDER_STATUS'],
                        'column1' => 'orderStatusId',
                        'column2' => 'id'
                    ],
                    [
                        'table1' => TABLES['ORDER'],
                        'table2' => TABLES['USER'],
                        'column1' => 'userId',
                        'column2' => 'id'
                    ]
                ]
            ];

            $stmt = getQueryByIdSQLPrepareStatement(
                $conn,
                $id,
                $projection,
                $join,
                TABLES['ORDER']
            );
            $stmt->setFetchMode(PDO::FETCH_OBJ);
            if (!$stmt->execute()) {
                throw new PDOException('Cannot execute query');
            }
            return Message::messageData(true, 'Get order by id successfully', [
                'order' => $stmt->fetch()
            ]);
        } catch (Exception $e) {
            print_r($e->getMessage());
            return Message::message(false, 'Something went wrong');
        }
    }

    public static function getOrders(
        $conn,
        $filter = [['field' => 'id', 'value' => '', 'like' => false]],
        $pagination = [],
        $sort =  ['sortBy' => 'createdAt', 'order' => 'ASC']
    ) {
        try {
            $filterConditions = [
                'id' => [['table' => TABLES['ORDER'], 'column' => 'id']],
                'customerName' => [['table' => TABLES['USER'], 'column' => 'firstName']],
                'totalPrice' => [[
                    'aggregate' => 'SUM',
                    'expression' =>
                    TABLES['ORDER_DETAIL'] . '.price' . ' * ' . TABLES['ORDER_DETAIL'] . '.quantity'
                ]],
                'status' => [['table' => TABLES['ORDER_STATUS'], 'column' => 'name']],
                'createdAt' => [['table' => TABLES['ORDER'], 'column' => 'createdAt']],
                'updatedAt' => [['table' => TABLES['ORDER'], 'column' => 'updatedAt']]
            ];

            $sortConditions = [
                'id' => [['table' => TABLES['ORDER'], 'column' => 'id']],
                'customerName' => [['table' => TABLES['USER'], 'column' => 'firstName']],
                'totalPrice' => [[
                    'aggregate' => 'SUM',
                    'expression' =>
                    TABLES['ORDER_DETAIL'] . '.price' . ' * ' . TABLES['ORDER_DETAIL'] . '.quantity'
                ]],
                'status' => [['table' => TABLES['ORDER_STATUS'], 'column' => 'name']],
                'createdAt' => [['table' => TABLES['ORDER'], 'column' => 'createdAt']],
                'updatedAt' => [['table' => TABLES['ORDER'], 'column' => 'updatedAt']]
            ];

            $sortCondition = $sortConditions[$sort['sortBy']];
            $orderBy = $sort['order'];
            for ($i = 0; $i < count($sortCondition); $i++) {
                $sortCondition[$i]['order'] = $orderBy;
            }

            $projection =  [
                [
                    'table' => TABLES['ORDER'],
                    'column' => 'id'
                ],
                [
                    'table' => TABLES['ORDER'],
                    'column' => 'shipAddress'
                ],
                [
                    'table' => TABLES['ORDER'],
                    'column' => 'phoneNumber'
                ],
                [
                    'aggregate' => 'SUM',
                    'expression' => TABLES['ORDER_DETAIL'] . '.price' . ' * ' . TABLES['ORDER_DETAIL'] . '.quantity',
                    'as' => 'totalPrice'
                ],
                [
                    'table' => TABLES['ORDER_STATUS'],
                    'column' => 'id',
                    'as' => 'statusId',
                ],
                [
                    'table' => TABLES['ORDER_STATUS'],
                    'column' => 'name',
                    'as' => 'statusName',
                ],
                [
                    'table' => TABLES['USER'],
                    'column' => 'id',
                    'as' => 'customerId'
                ],
                [
                    'table' => TABLES['USER'],
                    'column' => 'firstName',
                    'as' => 'customerFirstName'
                ],
                [
                    'table' => TABLES['USER'],
                    'column' => 'lastName',
                    'as' => 'customerLastName'
                ],
                [
                    'table' => TABLES['USER'],
                    'column' => 'imageUrl',
                    'as' => 'customerImageUrl'
                ],
                [
                    'table' => TABLES['USER'],
                    'column' => 'phoneNumber',
                    'as' => 'customerPhoneNumber'
                ],
                [
                    'table' => TABLES['USER'],
                    'column' => 'email',
                    'as' => 'customerEmail'
                ],
                [
                    'table' => TABLES['USER'],
                    'column' => 'address',
                    'as' => 'customerAddress'
                ],
                [
                    'table' => TABLES['ORDER'],
                    'column' => 'createdAt'
                ],
                [
                    'table' => TABLES['ORDER'],
                    'column' => 'updatedAt'
                ]
            ];
            $join =  [
                'tables' => [
                    TABLES['ORDER'],
                    TABLES['ORDER_DETAIL'],
                    TABLES['ORDER_STATUS'],
                    TABLES['PRODUCT'],
                    TABLES['USER']
                ],
                'on' => [
                    [
                        'table1' => TABLES['ORDER'],
                        'table2' => TABLES['ORDER_DETAIL'],
                        'column1' => 'id',
                        'column2' => 'orderId',
                    ],
                    [
                        'table1' => TABLES['ORDER_STATUS'],
                        'table2' => TABLES['ORDER'],
                        'column1' => 'id',
                        'column2' => 'orderStatusId',
                    ],
                    [
                        'table1' => TABLES['PRODUCT'],
                        'table2' => TABLES['ORDER_DETAIL'],
                        'column1' => 'id',
                        'column2' => 'productId',
                    ],
                    [
                        'table1' => TABLES['USER'],
                        'table2' => TABLES['ORDER'],
                        'column1' => 'id',
                        'column2' => 'userId',
                    ]
                ]
            ];
            $selection = array_map(function ($filterItem) {
                $selectionItem = [
                    'table' => TABLES['ORDER'],
                    'column' => $filterItem['field'],
                    'value' => $filterItem['value']
                ];
                if (isset($filterItem['like'])) {
                    $selectionItem['like'] = $filterItem['like'];
                }
                return $selectionItem;
            }, $filter);
            $group = [
                [
                    'table' => TABLES['ORDER_DETAIL'],
                    'column' => 'orderId'
                ]
            ];

            $stmt = getQuerySQLPrepareStatement(
                $conn,
                $projection,
                $join,
                $selection,
                $pagination,
                $sortCondition,
                $group
            );
            $stmt->setFetchMode(PDO::FETCH_OBJ);
            if (!$stmt->execute()) {
                throw new PDOException('Cannot execute query');
            }
            return Message::messageData(true, 'Get orders successfully', [
                'orders' => $stmt->fetchAll()
            ]);
        } catch (Exception $e) {
            return Message::message(false, 'Something went wrong');
        }
    }

    public static function updateOrder($conn, $id, $dataToUpdate)
    {
        try {
            $stmt = getUpdateByIdSQLPrepareStatement($conn, TABLES['ORDER'], $id, $dataToUpdate);
            if ($stmt->execute()) {
                return Message::message(true, 'Update order successfully');
            }
            throw new PDOException('Cannot execute sql statement');
        } catch (Exception $e) {
            $duplicateKey = getDuplicateKeyWhenSQLInsertUpdate($e);
            if (empty($duplicateKey)) {
                return Message::message(false, 'Something went wrong');
            }
            if ($duplicateKey[1] === 'name') {
                return Message::message(false, "'$duplicateKey[0]' is available");
            }
            return Message::message(false, 'Something went wrong');
        }
    }

    public static function getProductsOfOrder(
        $conn,
        $filter = [['field' => 'orderId', 'value' => '', 'like' => false]],
        $pagination = [],
        $sort =  [['sortBy' => 'createdAt', 'order' => 'ASC']]
    ) {
        try {
            $selectConditions = [
                'orderId' => ['table' => TABLES['ORDER'], 'column' => 'id'],
                'name' => ['table' => TABLES['PRODUCT'], 'column' => 'name'],
                'price' => ['table' => TABLES['ORDER_DETAIL'], 'column' => 'price'],
                'quantity' => ['table' => TABLES['ORDER_DETAIL'], 'column' => 'quantity'],
                'totalPrice' => ['table' => TABLES['ORDER_DETAIL'], 'column' => 'totalPrice'],
                'createdAt' => ['table' => TABLES['ORDER_DETAIL'], 'column' => 'createdAt'],
                'updatedAt' => ['table' => TABLES['ORDER_DETAIL'], 'column' => 'updatedAt']
            ];
            $sortConditions = [
                'name' => ['table' => TABLES['PRODUCT'], 'column' => 'name'],
                'price' => ['table' => TABLES['ORDER_DETAIL'], 'column' => 'price'],
                'quantity' => ['table' => TABLES['ORDER_DETAIL'], 'column' => 'quantity'],
                'totalPrice' => ['table' => TABLES['ORDER_DETAIL'], 'column' => 'totalPrice'],
                'createdAt' => ['table' => TABLES['ORDER_DETAIL'], 'column' => 'createdAt'],
                'updatedAt' => ['table' => TABLES['ORDER_DETAIL'], 'column' => 'updatedAt']
            ];

            $selection = [];
            foreach ($filter as $filterItem) {
                $selectCondition = $selectConditions[$filterItem['field']];
                $selectCondition['value'] = $filterItem['value'];
                if (isset($filterItem['like']) && $filterItem['like'] !== NULL) {
                    $selectCondition['like'] = $filterItem['like'];
                }
                $selection[] = $selectCondition;
            }

            $sorter = [];
            foreach ($sort as $sortItem) {
                $sortCondition = $sortConditions[$sortItem['sortBy']];
                $sortCondition['order'] = $sortItem['order'];
                $sorter[] = $sortCondition;
            }

            $projection =  [
                [
                    'table' => TABLES['ORDER'],
                    'column' => 'id',
                    'as' => 'orderId'
                ],
                [
                    'table' => TABLES['PRODUCT'],
                    'column' => 'id',
                    'as' => 'id'
                ],
                [
                    'table' => TABLES['PRODUCT'],
                    'column' => 'name',
                    'as' => 'name'
                ],
                [
                    'table' => TABLES['PRODUCT'],
                    'column' => 'imageUrl',
                ],
                [
                    'table' => TABLES['ORDER_DETAIL'],
                    'column' => 'price'
                ],
                [
                    'table' => TABLES['ORDER_DETAIL'],
                    'column' => 'quantity'
                ],
                [
                    'expression' => TABLES['ORDER_DETAIL'] . '.price' . ' * ' . TABLES['ORDER_DETAIL'] . '.quantity',
                    'as' => 'totalPrice'
                ],
                [
                    'table' => TABLES['ORDER_DETAIL'],
                    'column' => 'createdAt'
                ],
                [
                    'table' => TABLES['ORDER_DETAIL'],
                    'column' => 'updatedAt'
                ],
            ];

            $join =  [
                'tables' => [
                    TABLES['ORDER'],
                    TABLES['ORDER_DETAIL'],
                    TABLES['PRODUCT']
                ],
                'on' => [
                    [
                        'table1' => TABLES['ORDER'],
                        'table2' => TABLES['ORDER_DETAIL'],
                        'column1' => 'id',
                        'column2' => 'orderId'
                    ],
                    [
                        'table1' => TABLES['ORDER_DETAIL'],
                        'table2' => TABLES['PRODUCT'],
                        'column1' => 'productId',
                        'column2' => 'id'
                    ]
                ]
            ];

            $stmt = getQuerySQLPrepareStatement(
                $conn,
                $projection,
                $join,
                $selection,
                $pagination,
                $sorter,
            );
            $stmt->setFetchMode(PDO::FETCH_OBJ);
            if (!$stmt->execute()) {
                throw new PDOException('Cannot execute query');
            }
            return Message::messageData(true, 'Get products of order successfully', [
                'products' => $stmt->fetchAll()
            ]);
        } catch (Exception $e) {
            return Message::message(false, 'Something went wrong');
        }
    }

    public static function getOrderProduct(
        $conn,
        $filter = [
            ['field' => 'orderId', 'value' => ''],
            ['field' => 'productId', 'value' => '']
        ]
    ) {
        try {
            $selectConditions = [
                'orderId' => ['table' => TABLES['ORDER_DETAIL'], 'column' => 'orderId'],
                'productId' => ['table' => TABLES['ORDER_DETAIL'], 'column' => 'productId'],
            ];

            $selection = [];
            foreach ($filter as $filterItem) {
                $selectCondition = $selectConditions[$filterItem['field']];
                $selectCondition['value'] = $filterItem['value'];
                if (isset($filterItem['like']) && $filterItem['like'] !== NULL) {
                    $selectCondition['like'] = $filterItem['like'];
                }
                $selection[] = $selectCondition;
            }

            $projection =  [
                [
                    'table' => TABLES['ORDER_DETAIL'],
                    'column' => 'orderId',
                ],
                [
                    'table' => TABLES['ORDER_DETAIL'],
                    'column' => 'productId',
                ],
                [
                    'table' => TABLES['PRODUCT'],
                    'column' => 'name',
                    'as' => 'name'
                ],
                [
                    'table' => TABLES['PRODUCT'],
                    'column' => 'imageUrl',
                ],
                [
                    'table' => TABLES['ORDER_DETAIL'],
                    'column' => 'price'
                ],
                [
                    'table' => TABLES['ORDER_DETAIL'],
                    'column' => 'quantity'
                ],
                [
                    'expression' => TABLES['ORDER_DETAIL'] . '.price' . ' * ' . TABLES['ORDER_DETAIL'] . '.quantity',
                    'as' => 'totalPrice'
                ],
                [
                    'table' => TABLES['ORDER_DETAIL'],
                    'column' => 'createdAt'
                ],
                [
                    'table' => TABLES['ORDER_DETAIL'],
                    'column' => 'updatedAt'
                ]
            ];

            $join =  [
                'tables' => [
                    TABLES['ORDER_DETAIL'],
                    TABLES['PRODUCT']
                ],
                'on' => [
                    [
                        'table1' => TABLES['ORDER_DETAIL'],
                        'table2' => TABLES['PRODUCT'],
                        'column1' => 'productId',
                        'column2' => 'id'
                    ]
                ]
            ];

            $stmt = getQuerySQLPrepareStatement(
                $conn,
                $projection,
                $join,
                $selection,
            );
            $stmt->setFetchMode(PDO::FETCH_OBJ);
            if (!$stmt->execute()) {
                throw new PDOException('Cannot execute query');
            }
            return Message::messageData(true, 'Get order product successfully', [
                'orderProduct' => $stmt->fetch()
            ]);
        } catch (Exception $e) {
            return Message::message(false, 'Something went wrong');
        }
    }

    public static function updateOrderProduct(
        $conn,
        $orderId,
        $productId,
        $dataToUpdate
    ) {
        try {
            $stmt = getUpdateByMultiColumnsSQLPrepareStatement(
                $conn,
                TABLES['ORDER_DETAIL'],
                [
                    ['column' => 'orderId', 'value' => $orderId],
                    ['column' => 'productId', 'value' => $productId]
                ],
                $dataToUpdate
            );
            if ($stmt->execute()) {
                return Message::message(true, 'Update order product successfully');
            }
            throw new PDOException('Cannot execute sql statement');
        } catch (Exception $e) {
            return Message::message(false, 'Something went wrong');
        }
    }

    public static function deleteOrderProduct($conn, $orderId, $productId)
    {
        try {
            $stmt = getDeleteByMultiColumnsSQLPrepareStatement(
                $conn,
                TABLES['ORDER_DETAIL'],
                [
                    ['column' => 'orderId', 'value' => $orderId],
                    ['column' => 'productId', 'value' => $productId]
                ]
            );
            if ($stmt->execute()) {
                return Message::message(true, 'Delete order product successfully');
            }
            throw new PDOException('Cannot execute sql statement');
        } catch (Exception $e) {
            return Message::message(false, 'Something went wrong');
        }
    }

    public static function deleteByOrderProducts($conn, $ids)
    {
        try {
            foreach ($ids as $idPair) {
                $stmt = getDeleteByMultiColumnsSQLPrepareStatement(
                    $conn,
                    TABLES['ORDER_DETAIL'],
                    [
                        ['column' => 'orderId', 'value' => $idPair['orderId']],
                        ['column' => 'productId', 'value' => $idPair['productId']]
                    ]
                );
                if (!$stmt->execute()) {
                    throw new PDOException('Cannot execute sql statement');
                }
            }
            return Message::message(true, 'Delete order products successfully');
        } catch (Exception $e) {
            return Message::message(false, 'Something went wrong');
        }
    }

    public static function count(
        $conn,
        $filter = [['field' => 'id', 'value' => '', 'like' => false]]
    ) {
        try {
            $projection =  [
                [
                    'aggregate' => 'COUNT',
                    'expression' => '*',
                    'as' => 'total'
                ],
            ];
            $join =  [
                'tables' => [
                    TABLES['ORDER']
                ]
            ];
            $selection = array_map(function ($filterItem) {
                $selectionItem = [
                    'table' => TABLES['ORDER'],
                    'column' => $filterItem['field'],
                    'value' => $filterItem['value']
                ];
                if (isset($filterItem['like'])) {
                    $selectionItem['like'] = $filterItem['like'];
                }
                return $selectionItem;
            }, $filter);

            $stmt = getQuerySQLPrepareStatement(
                $conn,
                $projection,
                $join,
                $selection
            );

            $stmt->setFetchMode(PDO::FETCH_OBJ);
            if (!$stmt->execute()) {
                throw new PDOException('Cannot execute query');
            }
            return Message::messageData(true, 'Count products successfully', [
                'total' => $stmt->fetch()->total
            ]);
        } catch (Exception $e) {
            return Message::message(false, 'Something went wrong');
        }
    }
}
