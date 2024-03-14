<?php
require_once dirname(dirname(dirname(__DIR__))) . '/inc/init.php';
$conn = require_once dirname(dirname(dirname(__DIR__))) . '/inc/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
  throwStatusMessage(Message::message(false, 'Method must be GET'));
  return;
}

if (!isset($_GET['id'])) {
  throwStatusMessage(Message::message(false, '"id" is required'));
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
