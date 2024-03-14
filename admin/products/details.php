<?php
require_once  dirname(dirname(__DIR__)) . "/inc/init.php";
if (!isset($conn))
  $conn = require_once dirname(dirname(__DIR__)) . "/inc/db.php";

$productId = $_GET['id'];

$product = Product::getProductByIdForAdmin($conn, $productId);

if (!$product) {
  header('Location: 404.php');
  return;
}

?>

<?php require_once dirname(__DIR__) . "/inc/components/header.php" ?>;

<div class="page-wrapper">
  <div class="content">
    <div class="page-header">
      <div class="page-title">
        <h3>Product Details</h3>
        <h4>Full details of a product</h4>
      </div>
      <div class="page-btn">
        <a data-id="<?php echo $product->id ?>" id="delete-btn" class="btn btn-danger" href="javascript:void(0)">Delete</a>
      </div>
    </div>

    <div class="row g-5">
      <div class="col-lg-8 col-sm-12">
        <div class="card">
          <div class="card-body">
            <div class="productdetails">
              <ul class="product-bar">
                <li>
                  <h4>ID</h4>
                  <h6><?php echo $product->id ?></h6>
                </li>
                <li>
                  <h4>Name</h4>
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
        <div class="product-details-image">
          <img src="<?php echo $product->imageUrl ?>" alt="img" />
        </div>
      </div>
    </div>
    <div class="d-flex gap-3">
      <a class="btn btn-primary" href="<?php echo APP_URL; ?>/admin/products">Back</a>
    </div>
  </div>
</div>

<?php require_once dirname(__DIR__) . "/inc/components/footer.php" ?>;

<script>
  $(document).ready(function() {
    $('#delete-btn').on('click', function() {
      const id = $(this).data('id')
      Swal
        .fire({
          title: 'Delete This Product?',
          text: 'This action cannot be reverted. Are you sure?',
          showCancelButton: true,
          confirmButtonText: 'Delete',
          confirmButtonClass: 'btn btn-danger',
          cancelButtonClass: 'btn btn-cancel me-3 ms-auto',
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          buttonsStyling: !1,
          reverseButtons: true
        })
        .then(async function(result) {
          try {
            if (result.isConfirmed) {
              const response = await $.ajax({
                url: '<?php echo DELETE_PRODUCT_BY_ID_API ?>',
                type: 'POST',
                dataType: 'json',
                data: {
                  id
                },
              })

              if (response.status) {
                window.location.replace('<?php echo APP_URL; ?>/admin/products')
              } else {
                toastr.error('Something went wrong')
              }
            }
          } catch (error) {
            toastr.error('Something went wrong')
          }
        })
    })
  })
</script>