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

if (!isset($_POST['orderId']) || !isset($_POST['productId'])) {
  throwStatusMessage(Message::message(false, 'Missing required fields'));
  return;
}

$orderId = $_POST['orderId'];
$productId = $_POST['productId'];

$result = Order::deleteOrderProduct($conn, $orderId, $productId);

throwStatusMessage($result);
