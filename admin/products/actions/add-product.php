<?php
require_once dirname(dirname(dirname(__DIR__))) . '/inc/init.php';
$conn = require_once dirname(dirname(dirname(__DIR__))) . '/inc/db.php';
require_once dirname(dirname(dirname(__DIR__))) . '/inc/utils.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  throwStatusMessage(false, 'Method must be POST');
  return;
}
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

$product = new Product($productFields);

$createProductResult = $product->createProduct($conn);
if ($createProductResult['status']) {
  $response = Message::message(
    $createProductResult['status'],
    $createProductResult['message']
  );
  throwStatusMessage($response);
  return;
}
deleteFileByURL($uploadResult['url']); // delete new file uploaded
throwStatusMessage($createProductResult);
