<?php
require_once dirname(dirname(dirname(__DIR__))) . "/inc/init.php";
if (!isset($conn)) {
  $conn = require_once dirname(dirname(dirname(__DIR__))) . '/inc/db.php';
}

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
  throwStatusMessage(Message::message(false, 'Method must be GET'));
  return;
}

if (!isset($_GET['id'])) {
  throwStatusMessage(Message::message(false, '"id" is required'));
  return;
}

$id = $_GET['id'];

$result = Category::getCategoryById($coon, $id);

if (!$result['status']) {
  throwStatusMessage($result);
  return;
}

$category = $result['data']['category'];
if (!$category) {
  throwStatusMessage(Message::message(false, 'Category not found'));
  return;
}

throwStatusMessage($result);
