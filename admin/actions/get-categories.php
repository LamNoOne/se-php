<?php
session_start();

require_once  dirname(dirname(__DIR__)) . "/inc/init.php";
require_once dirname(dirname(__DIR__)) . '/inc/utils.php';
$conn = require_once dirname(dirname(__DIR__)) . '/inc/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
  throwStatusMessage(Message::message(false, 'Method must be GET'));
  return;
}


$page = 1;
$limit = 10;
$search = '';
$sortBy = 'createdAt';
$order = 'asc';

$dataToValidate = $_GET;

// $validateResult = Validator::integer($dataToValidate, [
//   'page',
//   'limit',
//   'search',
//   'draw'
// ]);
// if (!$validateResult['status']) {
//   throwStatusMessage(Message::message(false, $validateResult['status']));
// }

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

$categoriesOfAllPage = Category::getCategories(
  $conn,
  [
    ['field' => 'name', 'value' => $search, 'like' => true],
  ]
);

$categoriesPerPage = Category::getCategories(
  $conn,
  [
    ['field' => 'name', 'value' => $search, 'like' => true],
  ],
  ['offset' => ($page - 1)  * $limit, 'limit' => $limit],
  ['sortBy' => $sortBy, 'order' => $order],
);

$totalItems = count($categoriesOfAllPage);
$totalItemsPerPage = count($categoriesPerPage);
$response = [
  'totalItems' =>  $totalItems,
  'items' => $categoriesPerPage,
  'totalPages' => $totalItemsPerPage === 0 || ceil($totalItems / $totalItemsPerPage)
];

if (isset($_GET['draw'])) {
  $response['draw'] =  (int) $_GET['draw'];
}

throwStatusMessage($response);
