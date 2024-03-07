<?php
require_once dirname(__DIR__) . '/inc/init.php';
require_once dirname(__DIR__) . '/classes/controllers/product.php';
if (!isset($conn)) {
  $conn = require_once dirname(__DIR__) . '/inc/db.php';
}

$categories = Category::getAllCategories($conn);

?>

<?php require_once "./inc/components/header.php"; ?>

<div class="page-wrapper">
  <div class="content">
    <div class="page-header">
      <div class="page-title">
        <h4>Add Product</h4>
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
                    <input type="text" name="name" autofocus />
                  </div>
                </div>
                <div class="col-lg-12 col-sm-6 col-12">
                  <div class="form-group">
                    <label>Category</label>
                    <select name="category" class="select">
                      <option value="">Choose Category</option>
                      <?php foreach ($categories as $category) : ?>
                        <option value="<?php echo $category->id ?>">
                          <?php echo $category->name ?>
                        </option>
                      <?php endforeach; ?>
                    </select>
                  </div>
                </div>
                <div class="col-lg-12 col-sm-6 col-12">
                  <div class="form-group">
                    <label>Price</label>
                    <input type="number" name="price" />
                  </div>
                </div>
                <div class="col-lg-12 col-sm-6 col-12">
                  <div class="form-group">
                    <label>Quantity</label>
                    <input type="number" name="quantity" />
                  </div>
                </div>
              </div>
            </div>
            <div class="col-lg-9">
              <div class="row">
                <div class="col-lg-6 col-sm-6 col-12">
                  <div class="form-group">
                    <label>Screen</label>
                    <input type="text" name="screen" />
                  </div>
                </div>
                <div class="col-lg-6 col-sm-6 col-12">
                  <div class="form-group">
                    <label>Operating System</label>
                    <input type="text" name="operatingSystem" />
                  </div>
                </div>
                <div class="col-lg-6 col-sm-6 col-12">
                  <div class="form-group">
                    <label>Processor</label>
                    <input type="text" name="processor" />
                  </div>
                </div>
                <div class="col-lg-6 col-sm-6 col-12">
                  <div class="form-group">
                    <label>RAM</label>
                    <input type="number" name="ram" />
                  </div>
                </div>
                <div class="col-lg-6 col-sm-6 col-12">
                  <div class="form-group">
                    <label>Storage Capacity</label>
                    <input type="number" name="storageCapacity" />
                  </div>
                </div>
                <div class="col-lg-6 col-sm-6 col-12">
                  <div class="form-group">
                    <label>Weight</label>
                    <input type="number" name="weight" />
                  </div>
                </div>
                <div class="col-lg-6 col-sm-6 col-12">
                  <div class="form-group">
                    <label>Battery Capacity</label>
                    <input type="number" name="batteryCapacity" />
                  </div>
                </div>
                <div class="col-lg-6 col-sm-6 col-12">
                  <div class="form-group">
                    <label>Color</label>
                    <input type="text" name="color" />
                  </div>
                </div>
              </div>
            </div>
            <div class="col-lg-12">
              <div class="form-group">
                <label>Description</label>
                <textarea class="form-control" name="description"></textarea>
              </div>
            </div>
            <div class="col-lg-12">
              <div class="form-group">
                <label>Image</label>
                <div class="row">
                  <div class="col-lg-4">
                    <div class="image-upload">
                      <input type="file" name="image" accept="image/png, image/gif, image/jpeg" />
                      <div class="image-uploads">
                        <img src="assets/img/icons/upload.svg" alt="img" />
                        <h4>Drag and drop a image to upload</h4>
                      </div>
                    </div>
                  </div>
                  <div class="col-lg-8">
                    <div class="preview-image d-flex justify-content-center">
                      <img src="" alt="">
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-lg-12">
              <button type="submit" class="btn btn-submit me-2">Submit</button>
              <button class="btn btn-cancel">Cancel</button>
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
        category: {
          required: true
        },
        price: {
          required: true,
          number: true
        },
        quantity: {
          required: true,
          number: true
        },
        image: {
          required: true
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
          const formData = new FormData($(this)[0])

          const response = await $.ajax({
            url: 'actions/add-product.php',
            type: 'POST',
            dataType: 'json',
            data: formData,
            contentType: false,
            processData: false,
          })

          if (response.status) {
            window.location.replace(response.redirectUrl)
          } else {
            toastr.error(response.message)
          }
        }
      } catch (error) {
        toastr.error('Something went wrong')
      }
    })
  })
</script>