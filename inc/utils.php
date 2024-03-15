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

/**
 * This function to get SQL Select has contain placeholders, in order to SQL Prepare Statement, prevent SQL Injection.
 * @param array $projection The 2-dimension array contains the items you want to select in the query
 *  - $projection = [[
 *      'table' => string,
 *      'column' => string
 *  ]]
 * @param array $join The array contains information about tables and join conditions
 *  - $join = [
 *      'tables' => [string],
 *      'on' =>  [
 *          'table1' => string,
 *          'table2' => string,
 *          'column1' => string,
 *          'column2' => string
 *      ]
 *  ]
 * @param array $selection The 2-dimension array contains conditions for selecting records
 *  - $selection = [[
 *      'table' => string,
 *      'column' => string,
 *      'value' => string/number,
 *      'like' => boolean,
 *   ]];
 * @param array $pagination If true then enable limit-offset, otherwise disable limit-offset.
 *  - $pagination = boolean
 * @param array $sort The 2-dimension array contains information about the sort order of records.
 *  - $sort = [[
 *      'table' => string,
 *      'column' => string,
 *      'aggregate' => string,
 *      'expression' => string,
 *      'order' => 'ASC'/'DESC'
 *  ]];
 * @param array $group The 2-dimension array contains information about paging.
 *  - $group = [[
 *      'table' => string,
 *      'column' => string
 *  ]];
 */
function getPlaceholderQuerySQL($projection = [], $join = [], $selection = [], $pagination = false, $sort = [], $group = [])
{
    $sqlClauses = [];

    // handle select clause
    if (empty($projection)) {
        $sqlClauses[] = 'SELECT *';
    } else {
        $sqlClauses[] = "SELECT " . implode(', ', array_map(function ($projectionItem) {
            $as = '';
            if (isset($projectionItem['as']) && $projectionItem['as']) {
                $as = "AS `{$projectionItem['as']}`";
            }
            if (isset($projectionItem['column']) && $projectionItem['column']) {
                return "`{$projectionItem['table']}`.`{$projectionItem['column']}` $as";
            }
            // use aggregate function
            if (
                isset($projectionItem['aggregate'])
                && isset($projectionItem['expression'])
                && $projectionItem['aggregate']
                && $projectionItem['expression']
            ) {
                return "{$projectionItem['aggregate']}({$projectionItem['expression']}) $as";
            }
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
            "`{$tables[0]}` JOIN `{$tables[1]}` ON `{$on[0]['table1']}`.`{$on[0]['column1']}` = `{$on[0]['table2']}`.`{$on[0]['column2']}`"
        ];
        for ($i = 2; $i < count($tables); $i++) {
            $joinClauses[] = "JOIN `{$tables[$i]}` ON `{$on[$i - 1]['table1']}`.`{$on[$i - 1]['column1']}` = `{$on[$i - 1]['table2']}`.`{$on[$i - 1]['column2']}`";
        }
        $sqlClauses[] = 'FROM ' . implode(" ", $joinClauses);
    }


    // handle where clause
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

    if (!empty($group)) {
        $groupConditions = array_map(function ($groupItem) {
            return "{$groupItem['table']}.{$groupItem['column']}";
        }, $group);
        $sqlClauses[] = "GROUP BY " . implode(', ', $groupConditions);
    }

    // handle order by clause
    if (!empty($sort)) {
        $orderByConditions = array_map(function ($sortItem) {
            if (isset($sortItem['table']) && $sortItem['table'] && $sortItem['table'] !== '') {
                return "{$sortItem['table']}.{$sortItem['column']} {$sortItem['order']}";
            }
            if (isset($sortItem['column']) && $sortItem['column'] && $sortItem['column'] !== '') {
                return "{$sortItem['column']} {$sortItem['order']}";
            }
            // use aggregate function
            if (
                isset($sortItem['aggregate'])
                && $sortItem['aggregate']
                && $sortItem['aggregate'] !== ''
                && isset($sortItem['expression'])
                && $sortItem['expression']
                && $sortItem['expression'] !== ''
            ) {
                return "{$sortItem['aggregate']}({$sortItem['expression']}) {$sortItem['order']}";
            }
            return null;
        }, $sort);
        if (!array_search(null, $orderByConditions, true)) {
            $sqlClauses[] = 'ORDER BY ' . implode(', ', $orderByConditions);
        }
    }

    // handle limit offset clause
    if (
        isset($pagination)
        && $pagination
    ) {
        $sqlClauses[] = 'LIMIT :limit';
        $sqlClauses[] = 'OFFSET :offset';
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
 * This function to get SQL Select has contain placeholders, in order to SQL Prepare Statement, prevent SQL Injection.
 * @param array $projection The 2-dimension array contains the items you want to select in the query
 *  - $projection = [[
 *      'table' => string,
 *      'column' => string
 *  ]]
 * @param array $join The array contains information about tables and join conditions
 *  - $join = [
 *      'tables' => [string],
 *      'on' =>  [
 *          'table1' => string,
 *          'table2' => string,
 *          'column1' => string,
 *          'column2' => string
 *      ]
 *  ]
 * @param array $selection The 2-dimension array contains conditions for selecting records
 *  - $selection = [[
 *      'table' => string,
 *      'column' => string,
 *      'value' => string/number,
 *      'like' => boolean,
 *   ]];
 * @param array $pagination The array contains information about paging.
 *  - $pagination = ['limit' => int, 'offset' => int];
 * @param array $sort The 2-dimension array contains information about the sort order of records.
 *  - $sort = [[
 *      'table' => string,
 *      'column' => string,
 *      'aggregate' => string,
 *      'expression' => string,
 *      'order' => 'ASC'/'DESC'
 *  ]];
 * @param array $group The 2-dimension array contains information about paging.
 *  - $group = [[
 *      'table' => string,
 *      'column' => string
 *  ]];
 */
function getQuerySQLPrepareStatement(
    $conn,
    $projection = [],
    $join = [],
    $selection = [],
    $pagination = [],
    $sort =  [],
    $group = []
) {
    $enablePagination = false;
    if (
        isset($pagination['limit'])
        && isset($pagination['offset'])
        && $pagination['limit'] !== NULL
        && $pagination['offset'] !== NULL
    ) {
        $enablePagination = true;
    }

    $query = getPlaceholderQuerySQL(
        $projection,
        $join,
        $selection,
        $enablePagination,
        $sort,
        $group
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

    if (
        isset($pagination['limit'])
        && isset($pagination['offset'])
        && $pagination['limit'] !== NULL
        && $pagination['offset'] !== NULL
    ) {
        $stmt->bindValue(':limit', $pagination['limit'], PDO::PARAM_INT);
        $stmt->bindValue(':offset', $pagination['offset'], PDO::PARAM_INT);
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
