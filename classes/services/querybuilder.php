<?php
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

    protected function validateQueryOptions($options)
    {
        return $this->validation->validateQueryOptions($options);
    }

    protected function buildSqlQuery($tableName, $options)
    {
        // SQL query construction logic...
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
