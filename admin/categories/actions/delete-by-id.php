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

$result = Category::deleteCategory($conn, $id);

throwStatusMessage($result);
