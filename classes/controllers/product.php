<?php
require_once dirname(__DIR__) . "/services/message.php";
require_once dirname(__DIR__) . "/services/datafetcher.php";
require_once dirname(dirname(__DIR__)) . "/inc/utils.php";

class Product extends DataFetcher
{
    public $id;
    public $categoryId;
    public $name;
    public $imageUrl;
    public $description;
    public $screen;
    public $operatingSystem;
    public $processor;
    public $ram;
    public $storageCapacity;
    public $weight;
    public $batteryCapacity;
    public $color;
    public $price;
    public $stockQuantity;
    public $createdAt;
    public $updatedAt;

    public function __construct(
        $fields = []
    ) {
        $fields = Product::removeBannedFields($fields);
        $fields = deleteFieldsHasEmptyString($fields);
        foreach ($fields as $key => $value) {
            if (property_exists($this, $key)) {
                $this->{$key} = $value;
            }
        }
    }

    private static function removeBannedFields($fields)
    {
        $copiedFields = $fields;
        $bannedFields = ['id', 'createdAt', 'updatedAt'];
        foreach ($bannedFields as $bannedField) {
            if (array_key_exists($bannedField, $copiedFields)) {
                unset($copiedFields[$bannedField]);
            }
        }
        return $copiedFields;
    }

    private static function validateCreate($formData)
    {
        $result = Validator::required($formData, [
            'categoryId',
            'name',
            'imageUrl',
            'price',
            'stockQuantity',
        ]);
        if (!$result['status']) {
            return Message::message(false, $result['message']);
        }

        $result = Validator::integer($formData, [
            'categoryId',
            'ram',
            'storageCapacity',
            'batteryCapacity',
            'price',
            'stockQuantity',
        ]);
        if (!$result['status']) {
            return Message::message(false, $result['message']);
        }

        $result = Validator::float($formData, [
            'weight',
        ]);
        if (!$result['status']) {
            return Message::message(false, $result['message']);
        }

        $result = Validator::url($formData, [
            'imageUrl'
        ]);
        if (!$result['status']) {
            return Message::message(false, $result['message']);
        }

        return Message::message(true, 'Validate successfully');
    }

    private static function validateUpdate($formData)
    {
        $result = Validator::integer($formData, [
            'categoryId',
            'ram',
            'storageCapacity',
            'batteryCapacity',
            'price',
            'stockQuantity',
        ]);
        if (!$result['status']) {
            return Message::message(false, $result['message']);
        }

        $result = Validator::float($formData, [
            'weight',
        ]);
        if (!$result['status']) {
            return Message::message(false, $result['message']);
        }

        $result = Validator::url($formData, [
            'imageUrl'
        ]);
        if (!$result['status']) {
            return Message::message(false, $result['message']);
        }

        return Message::message(true, 'Validate successfully');
    }

    private static function validateGetById($formData)
    {
        $result = Validator::required($formData, [
            'id'
        ]);
        if (!$result['status']) {
            return Message::message(false, $result['message']);
        }

        $result = Validator::integer($formData, [
            'id'
        ]);
        if (!$result['status']) {
            return Message::message(false, $result['message']);
        }

        return Message::message(true, 'Validate successfully');
    }

    private static function validateDelete($formData)
    {
        $result = Validator::required($formData, [
            'id'
        ]);
        if (!$result['status']) {
            return Message::message(false, $result['message']);
        }

        $result = Validator::integer($formData, [
            'id'
        ]);
        if (!$result['status']) {
            return Message::message(false, $result['message']);
        }

        return Message::message(true, 'Validate successfully');
    }

    private static function validateDeleteByIds($formData)
    {
        $result = Validator::required($formData, [
            'ids'
        ]);
        if (!$result['status']) {
            return Message::message(false, $result['message']);
        }

        $result = Validator::array($formData, [
            'ids'
        ]);
        if (!$result['status']) {
            return Message::message(false, $result['message']);
        }

        return Message::message(true, 'Validate successfully');
    }

    public static function paginationQuery($query, $limit, $offset)
    {
        if (!isset($limit)) {
            throw new InvalidArgumentException("Limit is not defined");
        }

        is_null($offset) ?
            $query .= " LIMIT :limit"
            : $query .= " LIMIT :limit OFFSET :offset";
        return $query;
    }

    public function createProduct($conn)
    {
        try {
            // $validateResult = Product::validateCreate(get_object_vars($this));
            // if (!$validateResult['status']) {
            //     return Message::message(false, $validateResult['message']);
            // }

            $insert = "
                INSERT INTO `product`(`categoryId`, `name`, `description`, `imageUrl`, `screen`, `operatingSystem`, `processor`, `ram`, `storageCapacity`, `weight`, `batteryCapacity`, `color`, `price`, `stockQuantity`)
                VALUES (:categoryId, :name, :description, :imageUrl, :screen, :operatingSystem, :processor, :ram, :storageCapacity, :weight, :batteryCapacity, :color, :price, :stockQuantity)
            ";
            $stmt = $conn->prepare($insert);
            $status = $stmt->execute([
                ':categoryId' => $this->categoryId,
                ':name' => $this->name,
                ':description' => $this->description,
                ':imageUrl' => $this->imageUrl,
                ':screen' => $this->screen,
                ':operatingSystem' => $this->operatingSystem,
                ':processor' => $this->processor,
                ':ram' => $this->ram,
                ':storageCapacity' => $this->storageCapacity,
                ':weight' => $this->weight,
                ':batteryCapacity' => $this->batteryCapacity,
                ':color' => $this->color,
                ':price' => $this->price,
                ':stockQuantity' => $this->stockQuantity,
            ]);

            if (!$status) {
                throw new InvalidArgumentException('Add product failed');
            }

            return Message::messageData(true, 'Add product successfully', [
                'id' => $conn->lastInsertId()
            ]);
        } catch (Exception $e) {
            return Message::message(false, $e->getMessage());
        }
    }

    public static function updateProduct(
        $conn,
        $id,
        $dataToUpdate = []
    ) {
        try {
            // normalize before update
            $dataToUpdate = array_map(function ($value) {
                return $value === '' ? null : $value;
            }, $dataToUpdate);

            $validateResult = Product::validateUpdate($dataToUpdate);
            if (!$validateResult['status']) {
                return Message::message(false, $validateResult['message']);
            }

            // create dynamic update statement
            $sql = "UPDATE `product` SET ";
            foreach ($dataToUpdate as $key => $value) {
                if ($value === null) {
                    $sql .= "`$key` = NULL, ";
                } else {
                    $sql .= "`$key` = '$value', ";
                }
            }
            $sql = rtrim($sql, ', ');
            $sql .= " WHERE id = $id";

            $stmt = $conn->prepare($sql);
            $stmt->setFetchMode(PDO::FETCH_CLASS, 'Product');
            if ($stmt->execute()) {
                return Message::message(true, 'Update product successfully');
            }
            return Message::message(false, 'Update product failed');
        } catch (Exception $e) {
            return Message::message(false, $e->getMessage());
        }
    }

    public static function updateStockQuantity($conn, $productId, $stockQuantity)
    {
        try {
            $sql = "UPDATE `product` SET `stockQuantity` = :stockQuantity WHERE id = :productId";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(":stockQuantity", $stockQuantity, PDO::PARAM_INT);
            $stmt->bindParam(":productId", $productId, PDO::PARAM_INT);
            if ($stmt->execute()) {
                return true;
            }
            return false;
        } catch (Exception $e) {
            return false;
        }
    }

    public static function deleteProduct($conn, $id)
    {
        try {
            $validateResult = Product::validateDelete(['id' => $id]);
            if (!$validateResult['status']) {
                return Message::message(false, $validateResult['message']);
            }

            $query = "DELETE FROM product WHERE id = $id";

            $stmt = $conn->prepare($query);
            $stmt->setFetchMode(PDO::FETCH_OBJ);
            if ($stmt->execute()) {
                return Message::message(true, 'Delete product successfully');
            }
            return Message::messageData(true, 'Delete product failed');
        } catch (Exception $e) {
            return Message::messageData(true, 'Delete product failed');
        }
    }

    public static function deleteByIds($conn, $ids)
    {
        try {
            $validateResult = Product::validateDeleteByIds(['ids' => $ids]);
            if (!$validateResult['status']) {
                return Message::message(false, $validateResult['message']);
            }

            $stmt = getDeleteByIdsSQLPrepareStatement($conn, TABLES['PRODUCT'], $ids);
            if ($stmt->execute()) {
                return Message::message(true, 'Delete product by ids successfully');
            }
            return Message::message(true, 'Delete product by ids failed');
        } catch (Exception $e) {
            return Message::message(true, 'Delete product by ids failed');
        }
    }

    /**
     * @param mixed $conn
     * @param string | int $limit
     * @param string | int $offset
     * offset is optional
     */
    public static function getAllProducts($conn, $limit, $offset = null)
    {
        try {
            $query = "SELECT * FROM product";

            $query = self::paginationQuery($query, $limit, $offset);

            $stmt = $conn->prepare($query);
            $stmt->bindParam(":limit", $limit, PDO::PARAM_INT);

            if (!is_null($offset)) {
                $stmt->bindParam(":offset", $offset, PDO::PARAM_INT);
            }

            $stmt->setFetchMode(PDO::FETCH_OBJ);
            if (!$stmt->execute()) {
                throw new PDOException($stmt->errorInfo());
            }

            return $stmt->fetchAll();
        } catch (Exception $e) {
            return Message::message(false, "Can not get all products" . $e->getMessage());
        }
    }

    public static function getProductById($conn, $productId)
    {
        /**
         * Write your code here
         */

        try {
            $query = "SELECT * FROM product WHERE id = :productId";
            $stmt = $conn->prepare($query);
            $stmt->bindParam(":productId", $productId, PDO::PARAM_INT);
            $stmt->setFetchMode(PDO::FETCH_INTO, new Product());
            if (!$stmt->execute()) {
                throw new PDOException($stmt->errorInfo());
            }
            return $stmt->fetch();
        } catch (PDOException $e) {
            return Message::message(false, "Can not get products by id: " . $e->getMessage());
        }
    }

    /**
     * @param mixed $conn
     * @param string | int $categoryId
     */
    public static function getAllProductsByCondition($conn, $queryData = [])
    {
        try {
            $table = "product";
            $dataFetcher = new DataFetcher($conn);
            $products = $dataFetcher->fetchData($table, $queryData);
            return $products;
        } catch (PDOException $e) {
            return Message::message(false, "Can not get products by category: " . $e->getMessage());
        }
    }

    public static function getAllProductsForAdmin(
        $conn,
        $filter = [['field' => 'id', 'value' => '', 'like' => false]],
        $pagination = [],
        $sort =  ['sortBy' => 'createdAt', 'order' => 'ASC']
    ) {
        try {
            $projection =  [
                [
                    'table' => TABLES['PRODUCT'],
                    'column' => 'id'
                ],
                [
                    'table' => TABLES['PRODUCT'],
                    'column' => 'name'
                ],
                [
                    'table' => TABLES['PRODUCT'],
                    'column' => 'description'
                ],
                [
                    'table' => TABLES['PRODUCT'],
                    'column' => 'imageUrl'
                ],
                [
                    'table' => TABLES['PRODUCT'],
                    'column' => 'screen'
                ],
                [
                    'table' => TABLES['PRODUCT'],
                    'column' => 'operatingSystem'
                ],
                [
                    'table' => TABLES['PRODUCT'],
                    'column' => 'processor'
                ],
                [
                    'table' => TABLES['PRODUCT'],
                    'column' => 'ram'
                ],
                [
                    'table' => TABLES['PRODUCT'],
                    'column' => 'storageCapacity'
                ],
                [
                    'table' => TABLES['PRODUCT'],
                    'column' => 'weight'
                ],
                [
                    'table' => TABLES['PRODUCT'],
                    'column' => 'batteryCapacity'
                ],
                [
                    'table' => TABLES['PRODUCT'],
                    'column' => 'color'
                ],
                [
                    'table' => TABLES['PRODUCT'],
                    'column' => 'price'
                ],
                [
                    'table' => TABLES['PRODUCT'],
                    'column' => 'stockQuantity'
                ],
                [
                    'table' => TABLES['PRODUCT'],
                    'column' => 'createdAt'
                ],
                [
                    'table' => TABLES['PRODUCT'],
                    'column' => 'updatedAt'
                ],
                [
                    'table' => TABLES['CATEGORY'],
                    'column' => 'id',
                    'as' => 'categoryId'
                ],
                [
                    'table' => TABLES['CATEGORY'],
                    'column' => 'name',
                    'as' => 'categoryName'
                ],
            ];
            $join =  [
                'tables' => [
                    TABLES['PRODUCT'],
                    TABLES['CATEGORY'],
                ],
                'on' => [
                    [
                        'table1' => TABLES['PRODUCT'],
                        'table2' => TABLES['CATEGORY'],
                        'column1' => 'categoryId',
                        'column2' => 'id'
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
            $sort = [
                [
                    'table' => TABLES['PRODUCT'],
                    'column' => $sort['sortBy'],
                    'order' => $sort['order']
                ]
            ];

            $stmt = getQuerySQLPrepareStatement(
                $conn,
                $projection,
                $join,
                $selection,
                $pagination,
                $sort
            );

            $stmt->setFetchMode(PDO::FETCH_OBJ);
            if (!$stmt->execute()) {
                throw new PDOException('Cannot execute query');
            }
            return $stmt->fetchAll();
        } catch (Exception $e) {
            return Message::message(false, 'Get all products failed');
        }
    }

    public static function getProductByIdForAdmin($conn, $id)
    {
        try {
            $validateResult = Product::validateDelete(['id' => $id]);
            if (!$validateResult['status']) {
                return Message::message(false, $validateResult['message']);
            }

            $query = "
                SELECT p.id, p.name, p.description, p.imageUrl, p.screen, p.operatingSystem, p.processor, p.ram, p.storageCapacity, p.weight, p.batteryCapacity, p.color, p.price, p.stockQuantity, p.createdAt, p.updatedAt, c.id as categoryId, c.name as categoryName
                FROM product p JOIN category c on p.categoryId = c.id
                WHERE p.id = $id
            ";

            $stmt = $conn->prepare($query);
            $stmt->setFetchMode(PDO::FETCH_OBJ);
            if (!$stmt->execute()) {
                throw new PDOException('Cannot execute query');
            }
            return $stmt->fetch();
        } catch (Exception $e) {
            return Message::message(false, 'Get product by id failed');
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
                    TABLES['PRODUCT']
                ]
            ];
            $selection = array_map(function ($filterItem) {
                $selectionItem = [
                    'table' => TABLES['PRODUCT'],
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
            print_r($e->getMessage());
            return Message::message(false, 'Something went wrong');
        }
    }
}
