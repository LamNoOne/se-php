<?php
require_once dirname(dirname(dirname(__DIR__))) . '/inc/init.php';
$conn = require_once dirname(dirname(dirname(__DIR__))) . '/inc/db.php';
require_once dirname(dirname(dirname(__DIR__))) . '/inc/utils.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  throwStatusMessage(Message::message(false, 'Method must be POST'));
  return;
}
if (!isset($_POST['ids'])) {
  throwStatusMessage(Message::message(false, '"id" is required'));
  return;
}

$ids = $_POST['ids'];

$result = Order::deleteByOrderProducts($conn, $ids);
throwStatusMessage($result);
