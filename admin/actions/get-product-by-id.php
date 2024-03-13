<?php
session_start();

require_once dirname(dirname(__DIR__)) . '/inc/init.php';
$conn = require_once dirname(dirname(__DIR__)) . '/inc/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET' || !isset($_GET['id'])) {
  header('Location: products.php');
  return;
}

$productId = $_GET['id'];
$product = Product::getProductByIdForAdmin($conn, $productId);
if (!$product) {
  throwStatusMessage(Message::message(false, 'Product not found'));
  return;
}

throwStatusMessage(Message::messageData(true, 'Get product by id successfully', [
  'product' => $product
]));
