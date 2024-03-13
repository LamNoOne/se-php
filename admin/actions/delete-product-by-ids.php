<?php
session_start();

require_once dirname(dirname(__DIR__)) . '/inc/init.php';
$conn = require_once dirname(dirname(__DIR__)) . '/inc/db.php';
require_once dirname(dirname(__DIR__)) . '/inc/utils.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (!isset($_POST['ids'])) {
    return throwStatusMessage(Message::message(false, '"id" is required'));
  }

  $ids = $_POST['ids'];

  $deleteByIdsResult = Product::deleteByIds($conn, $ids);
  if ($deleteByIdsResult['status']) {
    return throwStatusMessage(Message::message(
      $deleteByIdsResult['status'],
      $deleteByIdsResult['message']
    ));
  }
  return throwStatusMessage($deleteByIdsResult);
}
