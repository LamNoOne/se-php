<?php
session_start();

require_once dirname(dirname(__DIR__)) . '/inc/init.php';
$conn = require_once dirname(dirname(__DIR__)) . '/inc/db.php';
require_once dirname(dirname(__DIR__)) . '/inc/utils.php';
/*

// Handle UI and edit product action

- choose image -> currentImageUrl = '', image not empty ---> REPLACE
- cancel image and choose image -> currentImageUrl = '', image not empty ---> REPLACE

- cancel -> currentImageUrl = '', image is empty ---> DELETE: develop in the future

- not cancel image, not choose image -> currentImageUrl != '' -> DO NOTHING

*/
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (!isset($_POST['id'])) {
    return throwStatusMessage(Message::message(false, 'ID is required'));
  }

  $id = $_POST['id'];
  $currentImageUrl = $_POST['currentImageUrl'];

  $foundProduct = Product::getProductById($conn, $id);
  if (!$foundProduct) {
    return throwStatusMessage(Message::message(false, 'Something went wrong'));
  }

  $uploadResult = null;
  if ($currentImageUrl === '') {
    $uploadResult = UploadFile::process('image');
  }

  // shoot error notification
  if (
    $uploadResult !== null
    && $uploadResult['status'] !== UPLOAD_ERR_OK
  ) {
    if ($uploadResult['status'] === UPLOAD_ERR_NO_FILE) {
      throwStatusMessage(Message::message(false, 'Image is required'));
      return;
    }
    throwStatusMessage(Message::message(false, $uploadResult['message']));
    return;
  }

  // update product in db
  $dataToUpdate = [...$_POST];
  unset($dataToUpdate['id']);
  unset($dataToUpdate['currentImageUrl']);
  if (
    $uploadResult !== null
    && $uploadResult['status'] === UPLOAD_ERR_OK
  ) {
    $dataToUpdate['imageUrl'] = $uploadResult['url'];
  }
  $updateProductResult = Product::updateProduct($conn, $id, $dataToUpdate);
  if ($updateProductResult['status']) {
    deleteFileByURL($foundProduct->imageUrl); // delete old file
    $message = Message::messageData(
      $updateProductResult['status'],
      $updateProductResult['message'],
      ['redirectUrl' => APP_URL . '/admin/products.php']
    );
    throwStatusMessage($message);
    return;
  }

  // delete new file was uploaded
  if (
    $uploadResult !== null
    && $uploadResult['status'] === UPLOAD_ERR_OK
  ) {
    deleteFileByURL($uploadResult['url']);
  }
  throwStatusMessage($updateProductResult);
}
