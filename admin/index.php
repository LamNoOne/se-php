<?php require_once "./inc/components/header.php" ?>;

<?php
if (!isset($conn))
  $conn = require_once dirname(__DIR__) . "/inc/db.php";

$orders = Order::getAllOrders($conn);
$products = Product::getAllProductsForAdmin($conn);

// print_r($products);

?>

<div class="page-wrapper">
  <div class="content">
    <div class="row">
      <div class="col-lg-3 col-sm-6 col-12 d-flex">
        <div class="dash-count">
          <div class="dash-counts">
            <h4><?php echo count($orders) ?></h4>
            <h5>Orders</h5>
          </div>
          <div class="dash-imgs">
            <i data-feather="file-text"></i>
          </div>
        </div>
      </div>
      <div class="col-lg-3 col-sm-6 col-12 d-flex">
        <div class="dash-count das1">
          <div class="dash-counts">
            <h4><?php echo count($products) ?></h4>
            <h5>Products</h5>
          </div>
          <div class="dash-imgs">
            <i class="fas fa-mobile-alt"></i>
          </div>
        </div>
      </div>
      <div class="col-lg-3 col-sm-6 col-12 d-flex">
        <div class="dash-count das2">
          <div class="dash-counts">
            <h4>100</h4>
            <h5>Customers</h5>
          </div>
          <div class="dash-imgs">
            <i data-feather="user-check"></i>
          </div>
        </div>
      </div>
      <div class="col-lg-3 col-sm-6 col-12 d-flex">
        <div class="dash-count das3">
          <div class="dash-counts">
            <h4>100</h4>
            <h5>Users</h5>
          </div>
          <div class="dash-imgs">
            <i data-feather="user"></i>
          </div>
        </div>
      </div>
      <div class="col-lg-12 col-sm-12 col-12 d-flex">
        <div class="card flex-fill">
          <div class="card-body">
            <h4 class="card-title">Recently Added Orders</h4>
            <div class="table-responsive dataview">
              <table class="table datatable">
                <thead>
                  <tr>
                    <th>No</th>
                    <th>Order ID</th>
                    <th>Customer</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Created At</th>
                  </tr>
                </thead>
                <tbody>
                  <?php $numberOrders = 4 ?>
                  <?php for ($i = 0; $i < $numberOrders; $i++) : ?>
                    <?php $order = $orders[$i] ?>
                    <tr>
                      <td><?php echo $i + 1 ?></td>
                      <td><a href="./product-details.html"><?php echo $order->id ?></a></td>
                      <td class="productimgname">
                        <a class="product-img" href="productlist.html">
                          <img src="<?php echo $order->imageUrl ?>" alt="product" />
                        </a>
                        <a href="product-details.html"><?php echo $order->firstName . ' ' . $order->lastName ?></a>
                      </td>
                      <td><?php echo $order->total ?></td>
                      <td>
                        <span class="badges <?php
                                            if ($order->status === 'Pending') {
                                              echo 'bg-lightred';
                                            } else {
                                              echo 'bg-lightgreen';
                                            }
                                            ?>"><?php echo $order->status ?></span>
                      </td>
                      <td><?php echo $order->createdAt ?></td>
                    </tr>
                  <?php endfor; ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
      <div class="col-lg-12 col-sm-12 col-12 d-flex">
        <div class="card flex-fill">
          <div class="card-body">
            <h4 class="card-title">Recently Added Products</h4>
            <div class="table-responsive dataview">
              <table class="table datatable">
                <thead>
                  <tr>
                    <th>No</th>
                    <th>Product ID</th>
                    <th>Name</th>
                    <th>Category</th>
                    <th>Created At</th>
                  </tr>
                </thead>
                <tbody>
                  <?php $numberProducts = 4 ?>
                  <?php for ($i = 0; $i < $numberProducts; $i++) : ?>
                    <?php $product = $products[$i] ?>
                    <tr>
                      <td><?php echo $i + 1 ?></td>
                      <td><a href="./product-details.html"><?php echo $product->id ?></a></td>
                      <td class="productimgname">
                        <a class="product-img" href="productlist.html">
                          <img src="<?php echo $product->imageUrl ?>" alt="product" />
                        </a>
                        <a href="product-details.html"><?php echo $product->name ?></a>
                      </td>
                      <td><?php echo $product->categoryName ?></td>
                      <td><?php echo $product->createdAt ?></td>
                    </tr>
                  <?php endfor; ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
      <div class="col-lg-12 col-sm-12 col-12 d-flex">
        <div class="card flex-fill">
          <div class="card-body">
            <h4 class="card-title">Recently Added Customers</h4>
            <div class="table-responsive dataview">
              <table class="table datatable">
                <thead>
                  <tr>
                    <th>No</th>
                    <th>Customer ID</th>
                    <th>Full Name</th>
                    <th>Phone</th>
                    <th>Email</th>
                    <th>Address</th>
                    <th>Created At</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td>1</td>
                    <td><a href="./product-details.html">IT0001</a></td>
                    <td class="productimgname">
                      <a class="product-img" href="productlist.html">
                        <img src="assets/img/product/product2.jpg" alt="product" />
                      </a>
                      <a href="product-details.html">Thomas</a>
                    </td>
                    <td>123456789</td>
                    <td>example@email.com</td>
                    <td>USA</td>
                    <td>12-12-2022</td>
                  </tr>
                  <tr>
                    <td>2</td>
                    <td><a href="javascript:void(0);">IT0002</a></td>
                    <td class="productimgname">
                      <a class="product-img" href="productlist.html">
                        <img src="assets/img/product/product3.jpg" alt="product" />
                      </a>
                      <a href="productlist.html">Miss</a>
                    </td>
                    <td>123456789</td>
                    <td>example@email.com</td>
                    <td>USA</td>
                    <td>25-11-2022</td>
                  </tr>
                  <tr>
                    <td>3</td>
                    <td><a href="javascript:void(0);">IT0003</a></td>
                    <td class="productimgname">
                      <a class="product-img" href="productlist.html">
                        <img src="assets/img/product/product4.jpg" alt="product" />
                      </a>
                      <a href="productlist.html">Tommy</a>
                    </td>
                    <td>123456789</td>
                    <td>example@email.com</td>
                    <td>USA</td>
                    <td>19-11-2022</td>
                  </tr>
                  <tr>
                    <td>4</td>
                    <td><a href="javascript:void(0);">IT0004</a></td>
                    <td class="productimgname">
                      <a class="product-img" href="productlist.html">
                        <img src="assets/img/product/product5.jpg" alt="product" />
                      </a>
                      <a href="productlist.html">Alexander</a>
                    </td>
                    <td>123456789</td>
                    <td>example@email.com</td>
                    <td>USA</td>
                    <td>20-11-2022</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
      <div class="col-lg-12 col-sm-12 col-12 d-flex">
        <div class="card flex-fill">
          <div class="card-body">
            <h4 class="card-title">Recently Added Users</h4>
            <div class="table-responsive dataview">
              <table class="table datatable">
                <thead>
                  <tr>
                    <th>No</th>
                    <th>User ID</th>
                    <th>Full Name</th>
                    <th>Username</th>
                    <th>Phone</th>
                    <th>Email</th>
                    <th>Address</th>
                    <th>Created At</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td>1</td>
                    <td><a href="./product-details.html">IT0001</a></td>
                    <td class="productimgname">
                      <a class="product-img" href="productlist.html">
                        <img src="assets/img/product/product2.jpg" alt="product" />
                      </a>
                      <a href="product-details.html">Thomas</a>
                    </td>
                    <td>thomas123</td>
                    <td>123456789</td>
                    <td>example@email.com</td>
                    <td>USA</td>
                    <td>12-12-2022</td>
                  </tr>
                  <tr>
                    <td>2</td>
                    <td><a href="./product-details.html">IT0002</a></td>
                    <td class="productimgname">
                      <a class="product-img" href="productlist.html">
                        <img src="assets/img/product/product2.jpg" alt="product" />
                      </a>
                      <a href="product-details.html">Thomas</a>
                    </td>
                    <td>thomas123</td>
                    <td>123456789</td>
                    <td>example@email.com</td>
                    <td>USA</td>
                    <td>21-05-2022</td>
                  </tr>
                  <tr>
                    <td>3</td>
                    <td><a href="./product-details.html">IT0003</a></td>
                    <td class="productimgname">
                      <a class="product-img" href="productlist.html">
                        <img src="assets/img/product/product2.jpg" alt="product" />
                      </a>
                      <a href="product-details.html">Thomas</a>
                    </td>
                    <td>thomas123</td>
                    <td>123456789</td>
                    <td>example@email.com</td>
                    <td>USA</td>
                    <td>21-05-2022</td>
                  </tr>
                  <tr>
                    <td>4</td>
                    <td><a href="./product-details.html">IT0004</a></td>
                    <td class="productimgname">
                      <a class="product-img" href="productlist.html">
                        <img src="assets/img/product/product2.jpg" alt="product" />
                      </a>
                      <a href="product-details.html">Thomas</a>
                    </td>
                    <td>thomas123</td>
                    <td>123456789</td>
                    <td>example@email.com</td>
                    <td>USA</td>
                    <td>21-05-2022</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>