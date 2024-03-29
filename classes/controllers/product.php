<?php
require_once dirname(__DIR__) . "/services/message.php";
require_once dirname(__DIR__) . "/services/datafetcher.php";
class Product extends DataFetcher
{

    public $id;
    public $categoryId;
    public $name;
    public $description;
    public $imageUrl;
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

    public function __construct()
    {
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

    public static function createProduct($conn, $userId)
    {
        /**
         * Write your code here
         * Validate admin
         */
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

            $stmt->setFetchMode(PDO::FETCH_CLASS, "Product");
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
            $stmt->setFetchMode(PDO::FETCH_CLASS, "Product");
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
    public static function getProductsByCategory($conn, $queryData = [])
    {
        try {
            $table = "product";
            $dataFetcher = DataFetcher::getInstance($conn);
            $products = $dataFetcher->fetchData($table, $queryData, "Product");
            return $products;
        } catch (PDOException $e) {
            return Message::message(false, "Can not get products by category: " . $e->getMessage());
        }
    }
}
