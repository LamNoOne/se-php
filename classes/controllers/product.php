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
        $categoryId = null,
        $name = null,
        $imageUrl = null,
        $description = null,
        $screen = null,
        $operatingSystem = null,
        $processor = null,
        $ram = null,
        $storageCapacity = null,
        $weight = null,
        $batteryCapacity = null,
        $color = null,
        $price = null,
        $stockQuantity = null
    ) {
        $this->categoryId = $categoryId;
        $this->name = $name;
        $this->imageUrl = $imageUrl;
        $this->description = $description;
        $this->screen = $screen;
        $this->operatingSystem = $operatingSystem;
        $this->processor = $processor;
        $this->ram = $ram;
        $this->storageCapacity = $storageCapacity;
        $this->weight = $weight;
        $this->batteryCapacity = $batteryCapacity;
        $this->color = $color;
        $this->price = $price;
        $this->stockQuantity = $stockQuantity;
    }

    private function validate()
    {

        $requiredRule = $this->categoryId
            && $this->name
            && $this->imageUrl
            && $this->price
            && $this->stockQuantity;
        if (!$requiredRule) {
            return [
                'status' => false,
                'message' => 'Missing required fields'
            ];
        }

        $numberRule = is_integer($this->categoryId)
            && is_integer($this->ram)
            && is_integer($this->storageCapacity)
            && is_numeric($this->weight)
            && is_integer($this->batteryCapacity)
            && is_integer($this->price)
            && is_integer($this->price);
        if (!$numberRule) {
            return [
                'status' => false,
                'message' => 'There are some fields are not valid numbers'
            ];
        }

        $notEmptyStringRule = !empty($this->name)
            && !empty($this->imageUrl)
            && !empty($this->description)
            && !empty($this->screen)
            && !empty($this->operatingSystem)
            && !empty($this->processor)
            && !empty($this->description)
            && !empty($this->color);
        if (!$notEmptyStringRule) {
            return [
                'status' => false,
                'message' => 'There are some fields are empty strings'
            ];
        }

        $status = $requiredRule && $numberRule && $notEmptyStringRule;
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
            $validateResult = $this->validate();
            if (!$validateResult['status']) {
                throw new InvalidArgumentException($validateResult['message']);
            }

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

            return Message::message(true, 'Add product successfully');
        } catch (Exception $e) {
            return Message::message(false, $e->getMessage());
        }
    }

    public static function updateProduct($conn, $userId)
    {
        /**
         * Write your code here
         * Validate admin
         */
    }

    public static function deleteProduct($conn, $userId)
    {
        /**
         * Write your code here
         * Validate admin
         */
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
        $filter = [],
        $sorter = ['id' => 'ASC'],
        $paginator = []
    ) {
        try {
            $sqlConditions = generateSQLConditions($filter, $sorter, $paginator);
            $query = "
                SELECT p.id, p.name, p.description, p.imageUrl, p.screen, p.operatingSystem, p.processor, p.ram, p.storageCapacity, p.weight, p.batteryCapacity, p.color, p.price, p.stockQuantity, p.createdAt, p.updatedAt, c.id as categoryId, c.name as categoryName
                FROM product as p JOIN category as c on p.categoryId = c.id
                {$sqlConditions['where']}
                {$sqlConditions['orderBy']}
                {$sqlConditions['limit']}
                {$sqlConditions['offset']}
            ";
            $stmt = $conn->prepare($query);
            $stmt->setFetchMode(PDO::FETCH_OBJ);
            if (!$stmt->execute()) {
                throw new PDOException('Cannot execute query');
            }
            return $stmt->fetchAll();
        } catch (Exception $e) {
            return Message::message(false, "Can not get all products" . $e->getMessage());
        }
    }

    public static function getProductByIdForAdmin($conn, $productId)
    {
        try {
            $query = "
                SELECT p.id, p.name, p.description, p.imageUrl, p.screen, p.operatingSystem, p.processor, p.ram, p.storageCapacity, p.weight, p.batteryCapacity, p.color, p.price, p.stockQuantity, p.createdAt, p.updatedAt, c.id as categoryId, c.name as categoryName
                FROM product p JOIN category c on p.categoryId = c.id
                WHERE p.id = $productId
            ";

            $stmt = $conn->prepare($query);
            $stmt->setFetchMode(PDO::FETCH_OBJ);
            if (!$stmt->execute()) {
                throw new PDOException('Cannot execute query');
            }
            return $stmt->fetch();
        } catch (Exception $e) {
            return Message::message(false, "Can not get product by id" . $e->getMessage());
        }
    }
}
