<?php
session_start();

require_once dirname(dirname(__DIR__)) . '/inc/init.php';
$conn = require_once dirname(dirname(__DIR__)) . '/inc/db.php';
require_once dirname(dirname(__DIR__)) . '/inc/utils.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $uploadResult = UploadFile::process('image');
  if ($uploadResult['status'] === UPLOAD_ERR_OK) {
    $productFields = [...$_POST, 'imageUrl' => $uploadResult['url']];
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
      deleteFileByURL($uploadResult['url']);
      throwStatusMessage($createProductResult);
    }
    return;
  }
  throwStatusMessage(Message::message(false, 'Image is required'));
}
