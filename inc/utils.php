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

function createFilter(string $key, mixed $value, string | null $operator = null): array
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
        foreach ($filter as $column => $value) {
            $filterConditions[] = "$column = $value";
        }
        $whereCondition = 'WHERE ' . implode(' AND ', $filterConditions);
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
