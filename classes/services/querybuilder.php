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
     * @param array $options [string $fields | array filters | string orderBy | int limit | int offset]
     * @param boolean $limit
     * Build the string query:
     * SELECT fields FROM table WHERE condition ORDER BY condition [Optional:LIMIT number OFFSET number]
     */
    protected function buildSqlQuery($tableName, $options, $limit = true)
    {
        // SQL query construction logic...
        // Create a base query
        $sql = "SELECT {$options['fields']} FROM $tableName";
        if (!empty($options['filters'])) {
            $sql .= " WHERE " . implode(
                ' AND ',
                array_map(
                    function ($key, $column) {
                        // Handle more logic
                        // If field of each filter is an array and not empty
                        if (is_array($column) && !empty($column)) {
                            // If values of field is a string
                            if (getType($column[0]) === "string")
                                return "$key IN ('" . implode("','", $column) . "')";
                            // Return original value if not string
                            return "$key IN (" . implode(",", $column) . ")";
                        }
                        // If field of each filter is not an array
                        return "$key = :$key";
                    },
                    array_keys($options['filters']),
                    array_values($options['filters'])
                )
            );
        }

        if (!empty($options['orderBy'])) {
            $sql .= " ORDER BY {$options['orderBy']}";
        }
        if ($limit) {
            $sql .= " LIMIT :limit OFFSET :offset";
        }

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
                // Handle $value is an array...
                if (!is_array($value))
                    $stmt->bindValue(":$column", $value, PDO::PARAM_INPUT_OUTPUT);
            }

            // Query with limit and offset is optional
            // Check if queryString contains a limit and offset parameters
            if (str_contains(strval($stmt->queryString), ':limit') && str_contains(strval($stmt->queryString), ':offset')) {
                $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
                $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            }

            if (!$stmt->execute())
                throw new PDOException("Can not execute query");
            return $stmt;
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }
}
