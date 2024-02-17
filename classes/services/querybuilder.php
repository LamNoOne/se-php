<?php
declare(strict_types=1);
require_once "validation.php";
class QueryBuilder extends Validation
{
    private $validation;
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
        $this->validation = new Validation();
    }

    /**
     * @param mixed $options
     * Validate options is an array, merge with default options if it's valid
     */
    protected function validateQueryOptions($options)
    {
        // Validate the query options
        return $this->validation->validateQueryOptions($options);
    }

    /**
     * @param string $tableName
     * @param mixed $options
     * Build the string query
     */
    protected function buildSqlQuery($tableName, $options)
    {
        // SQL query construction logic...
        // Create a base query
        $sql = "SELECT {$options['fields']} FROM $tableName";

        if (!empty($options['filters'])) {
            $sql .= " WHERE " . implode(' AND ', array_map(function ($column) {
                return "$column = :$column";
            }, array_keys($options['filters'])));
        }

        if (!empty($options['orderBy'])) {
            $sql .= " ORDER BY {$options['orderBy']}";
        }

        $sql .= " LIMIT :limit OFFSET :offset";

        return $sql;
    }

    /**
     * @param mixed $conn
     * @param string $sql
     * @param mixed $options
     * @return PDOStatement
     */
    protected function executeQuery($conn, $sql, $options)
    {
        // Query preparation and execution logic...
        try {
            $stmt = $conn->prepare($sql);

            $filters = $options['filters'];
            $limit = $options['limit'];
            $offset = $options['offset'];

            foreach ($filters as $column => $value) {
                $stmt->bindParam(":$column", $value);
            }

            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);

            if (!$stmt->execute())
                throw new PDOException("Can not execute query");
            return $stmt;
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }
}
