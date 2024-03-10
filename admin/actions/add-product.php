<?php
session_start();

require_once dirname(dirname(__DIR__)) . '/inc/init.php';
$conn = require_once dirname(dirname(__DIR__)) . '/inc/db.php';
require_once dirname(dirname(__DIR__)) . '/inc/utils.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $uploadResult = UploadFile::process('image');
  if ($uploadResult['status'] !== UPLOAD_ERR_OK) {
    if ($uploadResult['status'] === UPLOAD_ERR_NO_FILE) {
      throwStatusMessage(Message::message(false, 'Image is required'));
      return;
    }
    throwStatusMessage(Message::message(false, $uploadResult['message']));
    return;
  }

  $productFields = $_POST;
  $productFields['imageUrl'] = $uploadResult['url'];
  // delete fields that are not entered
  $productFields = array_filter($productFields, function ($productField) {
    return $productField !== '';
  });
  $product = new Product($productFields);

  $createProductResult = $product->createProduct($conn);
  if ($createProductResult['status']) {
    $response = Message::messageData(
      $createProductResult['status'],
      $createProductResult['message'],
      ['redirectUrl' => APP_URL . '/admin/products.php']
    );
    throwStatusMessage($response);
  } else {
    deleteFileByURL($uploadResult['url']); // delete new file uploaded
    throwStatusMessage($createProductResult);
  }
  return;
}
