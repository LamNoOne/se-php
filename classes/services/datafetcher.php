<?php
// Add Interface
// Use SingleTon and Factory Pattern
declare(strict_types=1);
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

    /**
     * @param mixed $conn
     * @return object
     */
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

    /**
     * @param string $tableName
     * @param mixed $options
     * @param string $fetchType
     * @return array
     */
    protected function fetchData($tableName, $options = [], $fetchType)
    {
        try {
            $options = $this->queryBuilder->validateQueryOptions($options);
            // Build and execute the fetch data with limit and offset
            $sql = $this->queryBuilder->buildSqlQuery($tableName, $options);
            $stmt = $this->queryBuilder->executeQuery($this->conn, $sql, $options);
            $data = $stmt->fetchAll(PDO::FETCH_CLASS, $fetchType);
            $currentRows = intval($options['limit']);
            // print_r("CURRENT ROWS: " . $currentRows . "<br />");

            // Get a number of rows
            $options['fields'] = 'COUNT(*)';
            $sqlGetRows = $this->queryBuilder->buildSqlQuery($tableName, $options, false);
            $stmtGetRows = $this->queryBuilder->executeQuery($this->conn, $sqlGetRows, $options);
            $dataRows = $stmtGetRows->fetchColumn();
            // print_r("DATA ROWS: " . $dataRows . "<br />");

            // In case of no results of current rows
            // TotalPage will be assigned to zero
            $totalPage = ceil($dataRows / $currentRows);

            return [
                'totalPage' => $totalPage,
                'data' => $data,
            ];
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }
}
