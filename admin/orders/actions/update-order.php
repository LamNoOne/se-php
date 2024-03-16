<?php
require_once dirname(dirname(dirname(__DIR__))) . '/inc/init.php';
$conn = require_once dirname(dirname(dirname(__DIR__))) . '/inc/db.php';
require_once dirname(dirname(dirname(__DIR__))) . '/inc/utils.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  return throwStatusMessage(Message::message(false, 'Method must be POST'));
}

if (!isset($_POST['id'])) {
  return throwStatusMessage(Message::message(false, '"id" is required'));
}

$id = $_POST['id'];

$result = Order::getOrderById($conn, $id);
if (!$result['status']) {
  return throwStatusMessage($result);
}

$order = $result['data']['order'];
if (!$order) {
  return throwStatusMessage(Message::message(false, 'Order not found'));
}

// update product in db
$dataToUpdate = $_POST;
unset($dataToUpdate['id']);

$updateResult = Order::updateOrder($conn, $id, $dataToUpdate);

throwStatusMessage($updateResult);
