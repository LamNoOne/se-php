<?php
require_once  dirname(__DIR__) . "/inc/init.php";
if (!isset($conn)) {
  $conn = require_once dirname(__DIR__) . '/inc/db.php';
}

$products = Product::getAllProductsForAdmin($conn);

?>

<?php require_once "./inc/components/header.php" ?>;

<div class="page-wrapper">
  <div class="content">
    <div class="page-header">
      <div class="page-title">
        <h3>Product List</h3>
        <h4>Manage your products</h4>
      </div>
      <div class="page-btn">
        <a href="add-product.php" class="btn btn-added box-shadow"><img src="assets/img/icons/plus.svg" alt="img" class="me-1" />Add New Product</a>
      </div>
    </div>

    <div class="card">
      <div class="card-body">
        <div class="table-top">
          <div class="search-set">
            <div class="search-path">
              <a class="btn btn-filter" id="filter_search">
                <img src="assets/img/icons/filter.svg" alt="img" />
                <span><img src="assets/img/icons/closes.svg" alt="img" /></span>
              </a>
            </div>
            <div class="search-input">
              <a class="btn btn-searchset"><img src="assets/img/icons/search-white.svg" alt="img" /></a>
            </div>
          </div>
          <div class="wordset">
            <ul>
              <li>
                <a data-bs-toggle="tooltip" data-bs-placement="top" title="pdf"><img src="assets/img/icons/pdf.svg" alt="img" /></a>
              </li>
              <li>
                <a data-bs-toggle="tooltip" data-bs-placement="top" title="excel"><img src="assets/img/icons/excel.svg" alt="img" /></a>
              </li>
              <li>
                <a data-bs-toggle="tooltip" data-bs-placement="top" title="print"><img src="assets/img/icons/printer.svg" alt="img" /></a>
              </li>
            </ul>
          </div>
        </div>

        <div class="card mb-0" id="filter_inputs">
          <div class="card-body pb-0">
            <div class="row">
              <div class="col-lg-12 col-sm-12">
                <div class="row">
                  <div class="col-lg col-sm-6 col-12">
                    <div class="form-group">
                      <select class="select">
                        <option>Choose Product</option>
                        <option>Macbook pro</option>
                        <option>Orange</option>
                      </select>
                    </div>
                  </div>
                  <div class="col-lg col-sm-6 col-12">
                    <div class="form-group">
                      <select class="select">
                        <option>Choose Category</option>
                        <option>Computers</option>
                        <option>Fruits</option>
                      </select>
                    </div>
                  </div>
                  <div class="col-lg col-sm-6 col-12">
                    <div class="form-group">
                      <select class="select">
                        <option>Choose Sub Category</option>
                        <option>Computer</option>
                      </select>
                    </div>
                  </div>
                  <div class="col-lg col-sm-6 col-12">
                    <div class="form-group">
                      <select class="select">
                        <option>Brand</option>
                        <option>N/D</option>
                      </select>
                    </div>
                  </div>
                  <div class="col-lg col-sm-6 col-12">
                    <div class="form-group">
                      <select class="select">
                        <option>Price</option>
                        <option>150.00</option>
                      </select>
                    </div>
                  </div>
                  <div class="col-lg-1 col-sm-6 col-12">
                    <div class="form-group">
                      <a class="btn btn-filters ms-auto"><img src="assets/img/icons/search-whites.svg" alt="img" /></a>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="table-responsive">
          <table class="table datanew">
            <thead>
              <tr>
                <th>
                  <label class="checkboxs">
                    <input type="checkbox" id="select-all" />
                    <span class="checkmarks"></span>
                  </label>
                </th>
                <th>No</th>
                <th>ID</th>
                <th>Name</th>
                <th>Price</th>
                <th>Quantity</th>
                <th>Category</th>
                <th>Created At</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
              <?php $count = 1 ?>
              <?php foreach ($products as $product) : ?>
                <tr>
                  <td>
                    <label class="checkboxs">
                      <input type="checkbox" />
                      <span class="checkmarks"></span>
                    </label>
                  </td>
                  <td><?php echo $count++ ?></td>
                  <td><?php echo $product->id ?></td>
                  <td class="productimgname">
                    <a href="<?php echo "product-details.php?id=$product->id" ?>" class="product-img">
                      <img src="<?php echo $product->imageUrl ?>" alt="Product image" />
                    </a>
                    <a class="text-linear-hover" href="<?php echo "product-details.php?id=$product->id" ?>">
                      <?php echo $product->name ?>
                    </a>
                  </td>
                  <td><?php echo $product->price ?></td>
                  <td><?php echo $product->stockQuantity ?></td>
                  <td><?php echo $product->categoryName ?></td>
                  <td><?php echo $product->createdAt ?></td>
                  <td>
                    <a class="me-3" href="<?php echo "product-details.php?id=$product->id" ?>">
                      <img src="assets/img/icons/eye.svg" alt="img" />
                    </a>
                    <a class="me-3" href="<?php echo "edit-product.php?id=$product->id" ?>">
                      <img src="assets/img/icons/edit.svg" alt="img" />
                    </a>
                    <a class="confirm-text" href="javascript:void(0);">
                      <img src="assets/img/icons/delete.svg" alt="img" />
                    </a>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

<?php require_once "./inc/components/footer.php" ?>;