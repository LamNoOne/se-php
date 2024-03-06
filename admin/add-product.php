<?php require_once "./inc/components/header.php" ?>;

<?php
if (!isset($conn)) {
  $conn = require_once dirname(__DIR__) . '/inc/db.php';
}

$categories = Category::getAllCategories($conn);

// print_r($categories);

?>

<div class="page-wrapper">
  <div class="content">
    <div class="page-header">
      <div class="page-title">
        <h4>Product Add</h4>
        <h6>Create new product</h6>
      </div>
    </div>

    <div class="card">
      <div class="card-body">
        <div class="row gx-5">
          <div class="col-lg-3">
            <div class="row">
              <div class="col-lg-12 col-sm-6 col-12">
                <div class="form-group">
                  <label>Product Name</label>
                  <input type="text" name="name" />
                </div>
              </div>
              <div class="col-lg-12 col-sm-6 col-12">
                <div class="form-group">
                  <label>Category</label>
                  <select class="select">
                    <option>Choose Category</option>
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
                  <input type="text" name="price" />
                </div>
              </div>
              <div class="col-lg-12 col-sm-6 col-12">
                <div class="form-group">
                  <label>Quantity</label>
                  <input type="text" name="quantity" />
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
                  <input type="text" name="ram" />
                </div>
              </div>
              <div class="col-lg-6 col-sm-6 col-12">
                <div class="form-group">
                  <label>Storage Capacity</label>
                  <input type="text" name="storageCapacity" />
                </div>
              </div>
              <div class="col-lg-6 col-sm-6 col-12">
                <div class="form-group">
                  <label>Weight</label>
                  <input type="text" name="weight" />
                </div>
              </div>
              <div class="col-lg-6 col-sm-6 col-12">
                <div class="form-group">
                  <label>Battery Capacity</label>
                  <input type="text" name="batteryCapacity" />
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
              <label> Product Image</label>
              <div class="image-upload">
                <input type="file" name="image" />
                <div class="image-uploads">
                  <img src="assets/img/icons/upload.svg" alt="img" />
                  <h4>Drag and drop a file to upload</h4>
                </div>
              </div>
            </div>
          </div>
          <div class="col-lg-12">
            <a href="javascript:void(0);" class="btn btn-submit me-2">Submit</a>
            <a href="productlist.html" class="btn btn-cancel">Cancel</a>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<?php require_once "./inc/components/footer.php" ?>;