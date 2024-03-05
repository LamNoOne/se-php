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
     * @param string $table
     * @param array $options [string $fields | array filters | string orderBy | int limit | int offset]
     * @param boolean $limit
     * Build the string query:
     * SELECT fields FROM table WHERE condition ORDER BY condition [Optional:LIMIT number OFFSET number]
     */
    protected function buildSqlQuery($table, $options, $limit = true)
    {
        // SQL query construction logic...
        // Create a base query
        $sql = "SELECT {$options['fields']} from $table";

        // Add JOIN clauses if provided
        if (!empty($options['joins'])) {
            foreach ($options['joins'] as $join) {
                $sql .= " {$join['type']} JOIN {$join['table']} ON {$join['on']}";
            }
        }

        // Add WHERE clause if filters are provided
        if (!empty($options['filters'])) {
            $whereClauses = [];
            foreach ($options['filters'] as $filter) {
                $column = $filter['column'];
                $operator = $filter['operator'];
                $value = $filter['value'];

                if ($operator === 'BETWEEN' && is_array($value) && count($value) === 2) {
                    $whereClauses[] = "$column BETWEEN $value[0] AND $value[1]";
                } elseif ($operator === 'IN' && is_array($value)) {
                    if (gettype($value[0] === 'string')) {
                        $whereClauses[] = "$column IN ('" . implode("','", $value) . "')";
                    } else {
                        $whereClauses[] = "$column IN (" . implode(",", $value) . ")";
                    }
                    // $placeholders = implode(', ', $value);
                } else {
                    isset($filter['alias']) ?
                        $whereClauses[] = "$column = :" . $filter['alias'] :
                        $whereClauses[] = "$column = :$column";
                }
            }
            $sql .= " WHERE " . implode(' AND ', $whereClauses);
        }

        // Add GROUP BY clause if provided
        if (!empty($options['groupBy'])) {
            $sql .= " GROUP BY {$options['groupBy']}";
        }

        // Add HAVING clause if provided
        if (!empty($options['having'])) {
            $sql .= " HAVING {$options['having']}";
        }

        // Add ORDER BY clause if provided
        if (!empty($options['orderBy'])) {
            $sql .= " ORDER BY {$options['orderBy']}";
        }

        // Add LIMIT and OFFSET clauses
        if ($limit) {
            $sql .= " LIMIT :limit OFFSET :offset";
        }
        return $sql;
    }

    /**
     * @param string $sql
     * @param mixed $options
     * @return PDOStatement
     */
    public function executeQuery($sql, $options)
    {
        // Query preparation and execution logic...
        try {
            $stmt = $this->conn->prepare($sql);

            $filters = $options['filters'];
            $limit = $options['limit'];
            $offset = $options['offset'];

            // print_r($filters);

            foreach ($filters as $filter) {
                // Handle $value is an array...
                if (!is_array($filter['value'])) {
                    isset($filter['alias']) ?
                        $stmt->bindValue(":" . $filter['alias'], $filter['value'], PDO::PARAM_INPUT_OUTPUT) :
                        $stmt->bindValue(":" . $filter['column'], $filter['value'], PDO::PARAM_INPUT_OUTPUT);
                }
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
