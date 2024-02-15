<?php
// Add Interface
require_once "querybuilder.php";
class DataFetcher extends QueryBuilder
{
    private static $instance;
    private $queryBuilder;
    private $conn;

    private function __construct($conn)
    {
        $this->conn = $conn;
        $this->queryBuilder = new QueryBuilder($conn);
    }

    public static function getInstance($conn): DataFetcher
    {
        if (!self::$instance) {
            self::$instance = new self($conn);
        }

        return self::$instance;
    }

    private function __clone()
    {
        // Disable cloning
    }

    protected function fetchData($tableName, $options = [], $fetchType)
    {
        try {
            $options = $this->queryBuilder->validateQueryOptions($options);
            $sql = $this->queryBuilder->buildSqlQuery($tableName, $options);
            $stmt = $this->queryBuilder->executeQuery($this->conn, $sql, $options);
            return $stmt->fetchAll(PDO::FETCH_CLASS, $fetchType);
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }
}
