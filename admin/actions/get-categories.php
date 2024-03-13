<?php
require_once  dirname(dirname(__DIR__)) . "/inc/init.php";
require_once dirname(dirname(__DIR__)) . '/inc/utils.php';
$conn = require_once dirname(dirname(__DIR__)) . '/inc/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
  throwStatusMessage(Message::message(false, 'Method must be GET'));
  return;
}

$categories = Category::getAllCategories($conn);

throwStatusMessage(Message::messageData(true, 'Get categories successfully', [
  'categories' => $categories
]));
