<?php
require_once dirname(dirname(dirname(__DIR__))) . '/inc/init.php';
$conn = require_once dirname(dirname(dirname(__DIR__))) . '/inc/db.php';
require_once dirname(dirname(dirname(__DIR__))) . '/inc/utils.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  throwStatusMessage(Message::message(false, 'Method must be POST'));
  return;
}

$data = $_POST;

$category = new Category($data);

$result = $category->createCategory($conn);
if ($result['status']) {
  throwStatusMessage(Message::message(true, 'Add category successfully'));
  return;
}
throwStatusMessage(Message::message(false, $result['message']));
