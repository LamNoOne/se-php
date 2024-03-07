<?php
require_once  dirname(__DIR__) . "/inc/init.php";
if (!isset($conn))
  $conn = require_once dirname(__DIR__) . "/inc/db.php";

$productId = $_GET['id'];

$product = Product::getProductByIdForAdmin($conn, $productId);

if (!$product) {
  header('Location: 404.php');
  return;
}

?>

<?php require_once "./inc/components/header.php" ?>;

<div class="page-wrapper">
  <div class="content">
    <div class="page-header">
      <div class="page-title">
        <h3>Product Details</h3>
        <h4>Full details of a product</h4>
      </div>
    </div>

    <div class="row g-5">
      <div class="col-lg-8 col-sm-12">
        <div class="card">
          <div class="card-body">
            <div class="productdetails">
              <ul class="product-bar">
                <li>
                  <h4>Product</h4>
                  <h6><?php echo $product->name ?></h6>
                </li>
                <li>
                  <h4>Category</h4>
                  <h6><?php echo $product->categoryName ?></h6>
                </li>
                <li>
                  <h4>Screen</h4>
                  <h6><?php echo $product->screen ?></h6>
                </li>
                <li>
                  <h4>Operating System</h4>
                  <h6><?php echo $product->operatingSystem ?></h6>
                </li>
                <li>
                  <h4>Processor</h4>
                  <h6><?php echo $product->processor ?></h6>
                </li>
                <li>
                  <h4>RAM</h4>
                  <h6><?php echo $product->ram . ' GB' ?></h6>
                </li>
                <li>
                  <h4>Storage Capacity</h4>
                  <h6><?php echo $product->storageCapacity . ' GB' ?></h6>
                </li>
                <li>
                  <h4>Weight</h4>
                  <h6><?php echo $product->weight . ' KG' ?></h6>
                </li>
                <li>
                  <h4>Battery Capacity</h4>
                  <h6><?php echo $product->batteryCapacity . ' mAh' ?></h6>
                </li>
                <li>
                  <h4>Color</h4>
                  <h6><?php echo $product->color ?></h6>
                </li>
                <li>
                  <h4>Price</h4>
                  <h6><?php echo $product->price ?></h6>
                </li>
                <li>
                  <h4>Stock Quantity</h4>
                  <h6><?php echo $product->stockQuantity ?></h6>
                </li>
                <li>
                  <h4>Description</h4>
                  <h6><?php echo $product->description ?></h6>
                </li>
              </ul>
            </div>
          </div>
        </div>
      </div>
      <div class="col-lg-4 col-sm-12">
        <div class="box-shadow">
          <img style="width: 100%; object-fit: cover;" src="<?php echo $product->imageUrl ?>" alt="img" />
        </div>
      </div>
    </div>
    <div>
      <a class="btn btn-submit" href="<?php echo APP_URL . "/admin/edit-product?id=$product->id" ?>">
        Go To Edit
      </a>
      <a class="btn btn-cancel" href="javascript:history.back()">Back</a>
    </div>
  </div>
</div>

<?php require_once "./inc/components/footer.php" ?>;