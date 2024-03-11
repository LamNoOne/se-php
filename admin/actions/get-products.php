<?php
require_once dirname(dirname(__DIR__)) . "/inc/init.php";

if (!isset($conn)) {
  $conn = require_once dirname(dirname(__DIR__)) . '/inc/db.php';
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
  $page = 1;
  $limit = 10;
  $search = null;

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

  $productsPerPage = Product::getAllProductsForAdmin(
    $conn,
    ['name' => $search],
    ['id' => 'ASC'],
    ['page' => $page, 'limit' => $limit]
  );
  $productsOfAllPage = Product::getAllProductsForAdmin(
    $conn,
    ['name' => $search],
    ['id' => 'ASC'],
  );

  $totalItems = count($productsOfAllPage);
  $response = [
    'totalItems' =>  $totalItems,
    'items' => $productsPerPage,
    'page' => (int) $page,
    'totalPages' => ceil($totalItems / $limit)
  ];

  if (isset($_GET['draw'])) {
    $response['draw'] =  (int) $_GET['draw'];
  }

  throwStatusMessage($response);
}
