<?php
require_once dirname(dirname(dirname(__DIR__))) . '/inc/init.php';
$conn = require_once dirname(dirname(dirname(__DIR__))) . '/inc/db.php';
require_once dirname(dirname(dirname(__DIR__))) . '/inc/utils.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  return throwStatusMessage(Message::message(false, 'Method must be POST'));
}

if (
  !isset($_POST['orderId'])
  && !isset($_POST['productId'])
  && !isset($_POST['quantity'])
) {
  return throwStatusMessage(Message::message(false, 'Missing required fields'));
}

$orderId = $_POST['orderId'];
$productId = $_POST['productId'];
$quantity = $_POST['quantity'];

if ($quantity < 1) {
  return throwStatusMessage(Message::message(false, '"quantity" must be greater than 0'));
}

$result = Order::getOrderProduct($conn, [
  ['field' => 'orderId', 'value' => $orderId],
  ['field' => 'productId', 'value' => $productId]
]);
if (!$result['status']) {
  return throwStatusMessage($result);
}

$orderProduct = $result['data']['orderProduct'];
if (!$orderProduct) {
  return throwStatusMessage(Message::message(false, 'Order product not found'));
}

// update order product in db
$dataToUpdate = $_POST;
$updateResult = Order::updateOrderProduct($conn, $orderId, $productId, $dataToUpdate);
throwStatusMessage($updateResult);
