<?php
require_once dirname(dirname(dirname(__DIR__))) . "/inc/init.php";
if (!isset($conn)) {
  $conn = require_once dirname(dirname(dirname(__DIR__))) . '/inc/db.php';
}

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
  return throwStatusMessage(Message::message(false, 'Method must be GET'));
}

if (!isset($_GET['userId'])) {
  return throwStatusMessage(Message::message(false, '"userId" is required'));
}

$page = 1;
$limit = 10;
$search = '';
$sortBy = 'createdAt';
$order = 'asc';
$userId = $_GET['userId'];

if (isset($_GET['page'])) {
  $page = $_GET['page'];
}
if (isset($_GET['limit'])) {
  $limit = $_GET['limit'];
}
if (isset($_GET['search'])) {
  $search = $_GET['search'];
}
if (isset($_GET['sortBy'])) {
  $sortBy = $_GET['sortBy'];
}
if (isset($_GET['order'])) {
  $order = $_GET['order'];
}

$getItemsOfAllPageResult = Order::getOrders(
  $conn,
  [
    ['field' => 'userId', 'value' => $userId],
    ['field' => 'id', 'value' => $search, 'like' => true]
  ]
);

if (!$getItemsOfAllPageResult['status']) {
  return throwStatusMessage($getItemsOfAllPageResult);
}

$itemsOfAllPage = $getItemsOfAllPageResult['data']['orders'];

$itemsPerPage = [];
if ($limit > 0) {
  $getItemsPerPageResult = Order::getOrders(
    $conn,
    [
      ['field' => 'userId', 'value' => $userId],
      ['field' => 'id', 'value' => $search, 'like' => true],
    ],
    ['offset' => ($page - 1)  * $limit, 'limit' => $limit],
    ['sortBy' => $sortBy, 'order' => $order]
  );
  if (!$getItemsPerPageResult['status']) {
    return throwStatusMessage($getItemsOfAllPageResult);
  }
  $itemsPerPage = $getItemsPerPageResult['data']['orders'];
}

$totalItems = count($itemsOfAllPage);
$items = ($limit > -1) ? $itemsPerPage : $itemsOfAllPage;
$totalItemsPerPage = count($items);

if ($totalItemsPerPage === 0 || $limit === 0) {
  $totalPages = 0;
} else {
  $totalPages = ($limit > 0) ? ceil($totalItems / $limit) : 1;
}

$response = [
  'totalItems' =>  $totalItems,
  'items' => $items,
  'totalPages' => $totalPages
];

if (isset($_GET['draw'])) {
  $response['draw'] =  (int) $_GET['draw'];
}

throwStatusMessage(Message::messageData(true, 'Get orders successfully', $response));
