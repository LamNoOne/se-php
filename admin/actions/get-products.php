<?php
require_once dirname(dirname(__DIR__)) . "/inc/init.php";

if (!isset($conn)) {
  $conn = require_once dirname(dirname(__DIR__)) . '/inc/db.php';
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
  $page = 1;
  $limit = 10;
  $search = '';
  $sortBy = 'createdAt';
  $order = 'asc';
  $lastAddId = null;

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
  if (isset($_GET['lastAddId'])) {
    $lastAddId = $_GET['lastAddId'];
  }

  $productsOfAllPage = Product::getAllProductsForAdmin(
    $conn,
    [
      ['field' => 'name', 'value' => $search, 'like' => true],
    ]
  );

  $totalItems = count($productsOfAllPage);
  // page is last page when has add product
  if ($lastAddId) {
    $page = ceil($totalItems / $limit);
  }

  $productsPerPage = Product::getAllProductsForAdmin(
    $conn,
    [
      ['field' => 'name', 'value' => $search, 'like' => true],
    ],
    ['offset' => ($page - 1)  * $limit, 'limit' => $limit],
    ['sortBy' => $sortBy, 'order' => $order],
  );

  $response = [
    'totalItems' =>  $totalItems,
    'items' => $productsPerPage,
  ];

  if (isset($_GET['draw'])) {
    $response['draw'] =  (int) $_GET['draw'];
  }

  throwStatusMessage($response);
}