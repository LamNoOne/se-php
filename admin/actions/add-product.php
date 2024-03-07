<?php
session_start();

require_once dirname(dirname(__DIR__)) . '/inc/init.php';
$conn = require_once dirname(dirname(__DIR__)) . '/inc/db.php';
require_once dirname(dirname(__DIR__)) . '/inc/utils.php';
require_once dirname(dirname(__DIR__)) . '/uploadfile.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $categoryId = $_POST['category'];
  $name = $_POST['name'];
  $description = $_POST['description'];
  $screen = $_POST['screen'];
  $operatingSystem = $_POST['operatingSystem'];
  $processor = $_POST['processor'];
  $ram = $_POST['ram'];
  $storageCapacity = $_POST['storageCapacity'];
  $weight = $_POST['weight'];
  $batteryCapacity = $_POST['batteryCapacity'];
  $color = $_POST['color'];
  $price = $_POST['price'];
  $stockQuantity = $_POST['quantity'];

  $uploadResult = upload_file('image');
  if ($uploadResult['status']) {
    $newProduct = new Product(
      $categoryId,
      $name,
      $uploadResult['url'],
      $description,
      $screen,
      $operatingSystem,
      $processor,
      $ram,
      $storageCapacity,
      $weight,
      $batteryCapacity,
      $color,
      $price,
      $stockQuantity
    );

    $createProductResult = $newProduct->createProduct($conn);
    if ($createProductResult['status']) {
      $response = [...$createProductResult, 'redirectUrl' => APP_URL . '/admin/products.php'];
      throwStatusMessage($response);
      exit();
    } else {
      deleteFileByURL($uploadResult['url']);
      throwStatusMessage($createProductResult);
      exit();
    }
  } else {
    throwStatusMessage($uploadResult);
    exit();
  }
}
