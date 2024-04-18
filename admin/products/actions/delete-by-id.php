<?php
require_once dirname(dirname(dirname(__DIR__))) . '/inc/init.php';
$conn = require_once dirname(dirname(dirname(__DIR__))) . '/inc/db.php';
require_once dirname(dirname(dirname(__DIR__))) . '/inc/utils.php';

Auth::requireLogin();
Auth::requireAdmin($conn);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  throwStatusMessage(Message::message(false, 'Method must be POST'));
  return;
}

if (!isset($_POST['id'])) {
  throwStatusMessage(Message::message(false, '"id" is required'));
  return;
}

$id = $_POST['id'];

$foundProduct = Product::getProductById($conn, $id);
if (!$foundProduct) {
  return throwStatusMessage(Message::message(false, 'Product not found'));
}

$deleteProductResult = Product::deleteProduct($conn, $id);
if ($deleteProductResult['status']) {
  return throwStatusMessage(Message::messageData(
    $deleteProductResult['status'],
    $deleteProductResult['message'],
    ['redirectUrl' => APP_URL . '/admin/products.php']
  ));
}
return throwStatusMessage($deleteProductResult);
