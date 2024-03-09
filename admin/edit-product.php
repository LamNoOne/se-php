<?php
require_once dirname(__DIR__) . '/inc/init.php';
require_once dirname(__DIR__) . '/classes/controllers/product.php';
if (!isset($conn)) {
  $conn = require_once dirname(__DIR__) . '/inc/db.php';
}

if ($_SERVER['REQUEST_METHOD'] !== 'GET' || !isset($_GET['id'])) {
  header('Location: products.php');
  return;
}

$productId = $_GET['id'];
$product = Product::getProductByIdForAdmin($conn, $productId);
if (!$product) {
  header('Location: 404.php');
  return;
}

$categories = Category::getAllCategories($conn);

?>

<?php require_once "./inc/components/header.php"; ?>

<div class="page-wrapper" style="top: 21px">
  <div class="content">
    <div class="page-header">
      <div class="page-title">
        <h3>Edit Product</h3>
        <h4>Update your product</h4>
      </div>
      <div class="page-btn">
        <a data-id="<?php echo $product->id ?>" id="delete-btn" class="btn btn-danger" href="javascript:void(0)">Delete</a>
      </div>
    </div>

    <div class="card">
      <div class="card-body">
        <form id="form" action="add-product.php" method="POST" enctype="multipart/form-data">
          <div class="row gx-5">
            <div class="col-lg-3">
              <div class="row">
                <div class="col-lg-12 col-sm-6 col-12">
                  <div class="form-group">
                    <label>Product Name</label>
                    <input type="text" name="name" value="<?php echo $product->name ?>" />
                  </div>
                </div>
                <div class="col-lg-12 col-sm-6 col-12">
                  <div class="form-group">
                    <label>Category</label>
                    <select name="categoryId" class="select">
                      <option value="">Choose Category</option>
                      <?php foreach ($categories as $category) : ?>
                        <option value="<?php echo $category->id ?>" <?php
                                                                    if ($category->id === $product->categoryId) {
                                                                      echo 'selected';
                                                                    }
                                                                    ?>>
                          <?php echo $category->name ?>
                        </option>
                      <?php endforeach; ?>
                    </select>
                  </div>
                </div>
                <div class="col-lg-12 col-sm-6 col-12">
                  <div class="form-group">
                    <label>Price</label>
                    <input type="number" name="price" value="<?php echo $product->price ?>" />
                  </div>
                </div>
                <div class="col-lg-12 col-sm-6 col-12">
                  <div class="form-group">
                    <label>Stock Quantity</label>
                    <input type="number" name="stockQuantity" value="<?php echo $product->stockQuantity ?>" />
                  </div>
                </div>
              </div>
            </div>
            <div class="col-lg-9">
              <div class="row">
                <div class="col-lg-6 col-sm-6 col-12">
                  <div class="form-group">
                    <label>Screen</label>
                    <input type="text" name="screen" value="<?php echo $product->screen ?>" />
                  </div>
                </div>
                <div class="col-lg-6 col-sm-6 col-12">
                  <div class="form-group">
                    <label>Operating System</label>
                    <input type="text" name="operatingSystem" value="<?php echo $product->operatingSystem ?>" />
                  </div>
                </div>
                <div class="col-lg-6 col-sm-6 col-12">
                  <div class="form-group">
                    <label>Processor</label>
                    <input type="text" name="processor" value="<?php echo $product->processor ?>" />
                  </div>
                </div>
                <div class="col-lg-6 col-sm-6 col-12">
                  <div class="form-group">
                    <label>RAM</label>
                    <input type="number" name="ram" value="<?php echo $product->ram ?>" />
                  </div>
                </div>
                <div class="col-lg-6 col-sm-6 col-12">
                  <div class="form-group">
                    <label>Storage Capacity</label>
                    <input type="number" name="storageCapacity" value="<?php echo $product->storageCapacity ?>" />
                  </div>
                </div>
                <div class="col-lg-6 col-sm-6 col-12">
                  <div class="form-group">
                    <label>Weight</label>
                    <input type="number" name="weight" value="<?php echo $product->weight ?>" />
                  </div>
                </div>
                <div class="col-lg-6 col-sm-6 col-12">
                  <div class="form-group">
                    <label>Battery Capacity</label>
                    <input type="number" name="batteryCapacity" value="<?php echo $product->batteryCapacity ?>" />
                  </div>
                </div>
                <div class="col-lg-6 col-sm-6 col-12">
                  <div class="form-group">
                    <label>Color</label>
                    <input type="text" name="color" value="<?php echo $product->color ?>" />
                  </div>
                </div>
              </div>
            </div>
            <div class="col-lg-12">
              <div class="form-group">
                <label>Description</label>
                <textarea class="form-control" name="description"><?php echo $product->description ?></textarea>
              </div>
            </div>
            <div class="col-lg-12">
              <div class="form-group">
                <label>Image</label>
                <div class="preview-image-wrapper mx-auto">
                  <div class="preview-image">
                    <div class="image">
                      <img src="">
                    </div>
                    <div class="content">
                      <div class="icon">
                        <i class="fas fa-cloud-upload-alt"></i>
                      </div>
                      <p class="text">No file chosen, yet!</p>
                    </div>
                    <div class="cancel-btn">
                      <i class="fas fa-times"></i>
                    </div>
                    <p class="file-name">File name here</p>
                    <input name="image" class="input-file" type="file">
                    <input name="currentImageUrl" class="current-image-url" value="<?php echo $product->imageUrl ?>" type="hidden">
                  </div>
                  <button class="choose-file-btn">Choose a image</button>
                </div>
              </div>
            </div>
          </div>
          <div class="col-lg-12 mt-5">
            <button type="submit" class="btn btn-submit me-2">Submit</button>
            <a href="products.php" class="btn btn-cancel">Cancel</a>
          </div>
      </div>
    </div>
    </form>
  </div>
</div>
</div>
</div>

<?php require_once "./inc/components/footer.php"; ?>

<script defer>
  $(document).ready(function() {
    $('#form').validate({
      rules: {
        name: {
          required: true
        },
        categoryId: {
          required: true
        },
        image: {
          required: true
        },
        price: {
          required: true,
          number: true
        },
        stockQuantity: {
          required: true,
          number: true
        },
        ram: {
          number: true
        },
        storageCapacity: {
          number: true
        },
        weight: {
          number: true
        },
        batteryCapacity: {
          number: true
        }
      },
    })

    $('#form').submit(async function(event) {
      try {
        event.preventDefault()
        if ($('#form').valid()) {
          const searchParams = new URLSearchParams(window.location.search)
          const formData = new FormData($(this)[0])
          formData.append('id', searchParams.get('id'))

          const response = await $.ajax({
            url: 'actions/edit-product.php',
            type: 'POST',
            dataType: 'json',
            data: formData,
            contentType: false,
            processData: false,
          })

          if (response.status) {
            window.location.replace(response.data.redirectUrl)
          } else {
            toastr.error(response.message)
          }
        }
      } catch (error) {
        toastr.error('Something went wrong')
      }
    })

    $('#delete-btn').on('click', function() {
      const id = $(this).data('id')
      Swal
        .fire(sweetalertDeleteConfirmConfig(
          'Delete This Product?',
          'This action cannot be reverted. Are you sure?'
        ))
        .then(async function(result) {
          try {
            if (result.isConfirmed) {
              const response = await $.ajax({
                url: 'actions/delete-product.php',
                type: 'POST',
                dataType: 'json',
                data: {
                  id
                },
              })

              if (response.status) {
                window.location.replace(response.data.redirectUrl)
              } else {
                toastr.error(response.message)
              }
            }
          } catch (error) {
            toastr.error('Something went wrong')
          }
        })
    })
  })
</script>