<?php
session_start();

require_once dirname(dirname(__DIR__)) . '/inc/init.php';
$conn = require_once dirname(dirname(__DIR__)) . '/inc/db.php';
require_once dirname(dirname(__DIR__)) . '/inc/utils.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (!isset($_POST['id'])) {
    return throwStatusMessage(Message::message(false, 'ID is required'));
  }

  $id = $_POST['id'];

  $foundProduct = Product::getProductById($conn, $id);
  if (!$foundProduct) {
    return throwStatusMessage(Message::message(false, 'Something went wrong'));
  }

  $uploadResult = UploadFile::process('image');

  if (
    $uploadResult['status'] === UPLOAD_ERR_OK
    || $uploadResult['status'] === UPLOAD_ERR_NO_FILE
  ) {

    $dataToUpdate = [...$_POST];
    if ($uploadResult['status'] !== UPLOAD_ERR_NO_FILE) {
      $dataToUpdate['imageUrl'] = $uploadResult['url'];
    }
    unset($dataToUpdate['id']);

    $updateProductResult = Product::updateProduct($conn, $id, $dataToUpdate);

    if ($updateProductResult['status']) {
      deleteFileByURL($foundProduct->imageUrl);
      $message = Message::messageData(
        $updateProductResult['status'],
        $updateProductResult['message'],
        ['redirectUrl' => APP_URL . '/admin/products.php']
      );
      throwStatusMessage($message);
    } else {
      if ($uploadResult['status'] !== UPLOAD_ERR_NO_FILE) {
        deleteFileByURL($uploadResult['url']);
      }
      throwStatusMessage($updateProductResult);
    }
    return;
  }
  throwStatusMessage($uploadResult);
  return;
}
