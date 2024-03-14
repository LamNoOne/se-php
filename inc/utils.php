<?php

#Redirect function if user logged in, default to index.php
function redirect($url = "")
{
    echo
    "<script>
        window.location.href='" . $url . "';
    </script>";
}

function verifyCategory($getCategoryId, $categoryId)
{
    return $getCategoryId === $categoryId;
}

#Handle status to return it to javascript fetch
function throwStatusMessage($status)
{
    $jsonStatus = json_encode($status);
    echo $jsonStatus;
}

function createFilter($key, $value, $operator = null)
{
    return ['column' => strval($key), 'operator' => strval($operator), 'value' => $value];
}

function generateSQLConditions(
    $filter = [],
    $sorter = ['id' => 'ASC'],
    $paginator = []
) {
    // handle filter
    $whereCondition = '';
    if (!empty($filter)) {
        $filterConditions = [];
        foreach ($filter as $filterItem) {
            if (!empty($filterItem['table']) && !empty($filterItem['column'])) {
                $filterConditions[] = "{$filterItem['table']}.{$filterItem['column']} LIKE '%{$filterItem['value']}%'";
            }
        }
        if (!empty($filterConditions)) {
            $whereCondition = 'WHERE ' . implode(' AND ', $filterConditions);
        }
    }

    // handle sorter
    $sortConditions = [];
    foreach ($sorter as $column => $order) {
        $sortConditions[] = "$column $order";
    }
    $orderByCondition = 'ORDER BY ' . implode(', ', $sortConditions);

    // handle paginator
    $limitCondition = '';
    $offsetCondition = '';
    if (!empty($paginator)) {
        if (!isset($paginator['limit'])) {
            throw new Exception('Invalid paginator param');
        }
        if (isset($paginator['page'])) {
            $offset = ($paginator['page'] - 1) * $paginator['limit'];
            $offsetCondition = 'OFFSET ' . $offset;
        }
        $limitCondition = 'LIMIT ' . $paginator['limit'];
    }

    return [
        'where' => $whereCondition,
        'orderBy' => $orderByCondition,
        'limit' => $limitCondition,
        'offset' => $offsetCondition
    ];
}

function getPlaceholderQuerySQL($projection = [], $join = [], $selection = [], $pagination = [], $sort = [])
{
    $sqlClauses = [];

    // handle select clause
    if (empty($projection)) {
        $sqlClauses[] = 'SELECT *';
    } else {
        $sqlClauses[] = "SELECT " . implode(', ', array_map(function ($projectionItem) {
            $as = '';
            if (isset($projectionItem['as']) && $projectionItem['as']) {
                $as = "AS {$projectionItem['as']}";
            }
            return "{$projectionItem['table']}.{$projectionItem['column']} $as";
        }, $projection));
    }

    // handle from clause
    if (!isset($join['tables'])) {
        throw new InvalidArgumentException('"$tables" is required');
    }
    if (!is_array($join['tables'])) {
        throw new InvalidArgumentException('"$tables" must be a array');
    }
    if (isset($join['on']) && !is_array($join['on'])) {
        throw new InvalidArgumentException('"$join[\'on\']" must be a array');
    }
    $tables = $join['tables'];
    if (count($tables) === 0) {
        throw new InvalidArgumentException('"$tables" must have at least 1 table');
    } else if (
        count($tables) === 1
        && !isset($join['on'])
    ) {
        $sqlClauses[] = 'FROM ' . $tables[0];
    } else if (isset($join['on']) && count($join['on']) < 1) {
        $sqlClauses[] = 'FROM ' . $tables[0];
    } else {
        $on = $join['on'];
        $joinClauses = [
            "{$tables[0]} JOIN {$tables[1]} ON {$on[0]['table1']}.{$on[0]['column1']} = {$on[0]['table2']}.{$on[0]['column2']}"
        ];
        for ($i = 2; $i < count($tables); $i++) {
            $joinClauses[] = "JOIN {$tables[$i]} ON {$on[$i - 1]['table1']}.{$on[$i - 1]['column1']} = {$on[$i - 1]['table2']}.{$on[$i - 1]['column2']}";
        }
        $sqlClauses[] = 'FROM ' . implode(" ", $joinClauses);
    }


    // handle where clause
    /**
        [
            [
                'table' => 'product',
                'column' => 'name',
                'value' => 'vn',
                'like' => true
            ]
        ]
     */
    $whereConditions = [];
    foreach ($selection as $index => $selectItem) {
        if ($selectItem['value'] === '') {
            continue;
        }
        $compareOperator = '=';
        $param = '';
        if (isset($selectItem['like'])) {
            if ($selectItem['like']) {
                $compareOperator = 'LIKE';
                $param = ":{$selectItem['column']}$index";
            }
        } else {
            $param = ":{$selectItem['value']}$index";
        }

        if (isset($selectItem['table'])) {
            $whereConditions[] = "{$selectItem['table']}.{$selectItem['column']} $compareOperator $param";
        } else {
            if ($selectItem['table'] === '') {
                $whereConditions[] = "{$selectItem['column']} $compareOperator $param";
            } else {
                $whereConditions[] = "{$selectItem['table']}.{$selectItem['column']} $compareOperator $param";
            }
        }
    }
    if (!empty($whereConditions)) {
        $sqlClauses[] = 'WHERE ' . implode(' AND ', $whereConditions);
    }

    // handle order by clause
    if (!empty($sort)) {
        $orderByConditions = array_map(function ($sortItem) {
            if (isset($sortItem['table']) && !$sortItem['table'] !== '') {
                return "{$sortItem['table']}.{$sortItem['column']} {$sortItem['order']}";
            } else {
                return "{$sortItem['column']} {$sortItem['order']}";
            }
        }, $sort);
        $sqlClauses[] = 'ORDER BY ' . implode(', ', $orderByConditions);
    }

    // handle limit offset clause
    if (
        isset($pagination['limit'])
        && isset($pagination['offset'])
        && $pagination['limit'] !== NULL
        && $pagination['offset'] !== NULL
    ) {
        $sqlClauses[] = 'LIMIT ' . $pagination['limit'];
        $sqlClauses[] = 'OFFSET ' . $pagination['offset'];
    }

    return implode(' ', $sqlClauses);
}

function getPlaceholderQueryByIdSQL($projection = [], $join = [])
{
    $sqlClauses = [];

    // handle select clause
    if (empty($projection)) {
        $sqlClauses[] = 'SELECT *';
    } else {
        $sqlClauses[] = "SELECT " . implode(', ', array_map(function ($projectionItem) {
            $as = '';
            if (isset($projectionItem['as']) && $projectionItem['as']) {
                $as = "AS {$projectionItem['as']}";
            }
            return "{$projectionItem['table']}.{$projectionItem['column']} $as";
        }, $projection));
    }

    // handle from clause
    if (!isset($join['tables'])) {
        throw new InvalidArgumentException('"$tables" is required');
    }
    if (!is_array($join['tables'])) {
        throw new InvalidArgumentException('"$tables" must be a array');
    }
    if (isset($join['on']) && !is_array($join['on'])) {
        throw new InvalidArgumentException('"$join[\'on\']" must be a array');
    }
    $tables = $join['tables'];
    if (count($tables) === 0) {
        throw new InvalidArgumentException('"$tables" must have at least 1 table');
    } else if (
        count($tables) === 1
        && !isset($join['on'])
    ) {
        $sqlClauses[] = 'FROM ' . $tables[0];
    } else if (isset($join['on']) && count($join['on']) < 1) {
        $sqlClauses[] = 'FROM ' . $tables[0];
    } else {
        $on = $join['on'];
        $joinClauses = [
            "{$tables[0]} JOIN {$tables[1]} ON {$on[0]['table1']}.{$on[0]['column1']} = {$on[0]['table2']}.{$on[0]['column2']}"
        ];
        for ($i = 2; $i < count($tables); $i++) {
            $joinClauses[] = "JOIN {$tables[$i]} ON {$on[$i - 1]['table1']}.{$on[$i - 1]['column1']} = {$on[$i - 1]['table2']}.{$on[$i - 1]['column2']}";
        }
        $sqlClauses[] = 'FROM ' . implode(" ", $joinClauses);
    }

    // handle where clause
    $sqlClauses[] = 'WHERE id = :id';

    return implode(' ', $sqlClauses);
}

/**
$sort =  [
        'table' => 'product',
        'column' => 'createdAt',
        'order' => 'ASC'
    ]
 */
function getQuerySQLPrepareStatement(
    $conn,
    $projection = [],
    $join = [],
    $selection = [['table' => '', 'column' => 'id', 'value' => '', 'like' => false]],
    $pagination = [],
    $sort =  [[
        'table' => '',
        'column' => 'createdAt',
        'order' => 'ASC'
    ]]
) {
    $paginationForGetSQL = [];
    if (isset($pagination['offset']) && isset($pagination['limit'])) {
        $paginationForGetSQL = [
            'offset' => ":offset",
            'limit' => ":limit"
        ];
    }

    $query = getPlaceholderQuerySQL(
        $projection,
        $join,
        $selection,
        $paginationForGetSQL,
        $sort
    );

    $stmt = $conn->prepare($query);

    foreach ($selection as $index => $selectionItem) {
        if ($selectionItem['value'] === '') {
            continue;
        }
        $param = ":{$selectionItem['column']}$index";
        if ($selectionItem['like']) {
            $stmt->bindValue($param, '%' . $selectionItem['value'] . '%', PDO::PARAM_STR);
        } else {
            if (is_numeric($selectionItem['value'])) {
                $stmt->bindValue($param, $selectionItem['value'], PDO::PARAM_INT);
            } else {
                $stmt->bindValue($param, $selectionItem['value'], PDO::PARAM_STR);
            }
        }
    }

    if (isset($pagination['offset']) && isset($pagination['limit'])) {
        $stmt->bindValue(':offset', $pagination['offset'], PDO::PARAM_INT);
        $stmt->bindValue(':limit', $pagination['limit'], PDO::PARAM_INT);
    }

    return $stmt;
}

function getQueryByIdSQLPrepareStatement(
    $conn,
    $id,
    $projection = [],
    $join = []
) {
    $query = getPlaceholderQueryByIdSQL(
        $projection,
        $join
    );
    $stmt = $conn->prepare($query);
    $stmt->bindValue(':id', $id, PDO::PARAM_INT);
    return $stmt;
}

function getDeleteByIdSQLPrepareStatement($conn, $table, $id)
{
    $sql = "DELETE FROM $table WHERE id = :id";
    $stmt = $conn->prepare($sql);
    $stmt->bindValue(':id', $id, PDO::PARAM_INT);
    return $stmt;
}

function getDeleteByIdsSQLPrepareStatement($conn, $table, $ids)
{
    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    $sql = "DELETE FROM $table WHERE id IN ($placeholders)";
    $stmt = $conn->prepare($sql);
    foreach ($ids as $key => $id) {
        $stmt->bindValue($key + 1, $id, PDO::PARAM_INT);
    }
    return $stmt;
}

function getCreateSQLPrepareStatement($conn, $table, $object)
{
    $reflection = new ReflectionObject($object);
    $properties = $reflection->getProperties();

    $array = [];
    $columns = [];
    foreach ($properties as $property) {
        $property->setAccessible(true);
        $array[$property->getName()] = $property->getValue($object);
        $columns[] = $property->getName();
    }

    $insertStatement = "INSERT INTO `$table`";
    $insertStatement .= '(`' . implode('`, `', $columns) . '`) VALUES ';
    $insertStatement .= '(:' . implode(', :', $columns) . ')';

    $stmt = $conn->prepare($insertStatement);
    foreach ($array as $key => $value) {
        $stmt->bindValue(":$key", $value, PDO::PARAM_STR);
    }
    return $stmt;
}

function getUpdateByIdSQLPrepareStatement($conn, $table, $id, $dataToUpdate = [])
{
    $keyValuePairs = [];
    foreach ($dataToUpdate as $key => $value) {
        $keyValuePairs[] = "`$key`=:$key";
    }
    $updateStatement = "UPDATE `$table` SET " . implode(',', $keyValuePairs) . " WHERE id=:id";
    $stmt = $conn->prepare($updateStatement);
    foreach ($dataToUpdate as $key => $value) {
        $stmt->bindValue(":$key", $value, PDO::PARAM_STR);
    }
    $stmt->bindValue(":id", $id, PDO::PARAM_INT);
    return $stmt;
}

function getDuplicateKeyWhenSQLInsertUpdate($exceptionError)
{
    $errorCode = $exceptionError->getCode();
    $errorMessage = $exceptionError->getMessage();
    $duplicateKey = [];
    if (!$errorCode === '23000') {
        return $duplicateKey;
    }

    $pattern = "/Duplicate entry '([^']*)' for key '([^']*)'/";
    if (preg_match($pattern, $errorMessage, $matches)) {
        $duplicateKey[] = $matches[1];
        $duplicateKey[] = $matches[2];
    }
    return $duplicateKey;
}

function deleteFileByURL($url)
{
    $pathToDelete = $_SERVER['DOCUMENT_ROOT'] . parse_url($url)['path'];
    if (file_exists($pathToDelete)) {
        unlink($pathToDelete);
        return;
    }
}

function checkFileExistsInLocalByURL($url)
{
    $pathToDelete = $_SERVER['DOCUMENT_ROOT'] . parse_url($url)['path'];
    if (file_exists($pathToDelete)) {
        return true;
    }
    return false;
}

function deleteFieldsHasEmptyString($array)
{
    return array_filter($array, function ($value) {
        return $value !== '';
    });
}

// Handle get order status

function pendingStatus()
{
    return "#ea5455";
}

function paidStatus()
{
    return "#28c76f";
}

function deliveringStatus()
{
    return "#28c76f";
}

function deliveredStatus()
{
    return "#28c76f";
}

function getStatusController()
{
    return [
        "paid" => "paidStatus",
        "pending" => "pendingStatus",
        "delivering" => "deliveringStatus",
        "delivered" => "deliveredStatus"
    ];
}


function getStatus($status)
{
    return getStatusController()[strtolower($status)]();
}
