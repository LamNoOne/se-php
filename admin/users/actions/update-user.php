<?php
require_once dirname(dirname(dirname(__DIR__))) . "/inc/init.php";
if (!isset($conn)) {
  $conn = require_once dirname(dirname(dirname(__DIR__))) . '/inc/db.php';
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  throwStatusMessage(Message::message(false, 'Method must be POST'));
  return;
}

if (!isset($_POST['id'])) {
  throwStatusMessage(Message::message(false, '"id" is required'));
  return;
}

$id = $_POST['id'];
$dataToUpdate = $_POST;
unset($dataToUpdate['id']);

$result = User::updateUserV2($conn, $id, $dataToUpdate);
throwStatusMessage($result);
