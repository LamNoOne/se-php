<?php require_once "./inc/components/header.php" ?>;

<?php
if (!isset($conn))
  $conn = require_once dirname(__DIR__) . "/inc/db.php";

$sorter = array(
  'createdAt' => 'DESC',
  'updatedAt' => 'DESC'
);

$orders = Order::getAllOrders($conn, [], $sorter);
$products = Product::getAllProductsForAdmin($conn, [], $sorter);
$customers = User::getAllUsers($conn, ['roleId' => 3], $sorter);
$users = User::getAllUsers($conn, [], $sorter);

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
            <h4><?php echo count($customers) ?></h4>
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
            <h4><?php echo count($users) ?></h4>
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
              <table class="table">
                <thead>
                  <tr>
                    <th>Order ID</th>
                    <th>Customer</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Created At</th>
                  </tr>
                </thead>
                <tbody>
                  <?php $count = 1 ?>
                  <?php foreach ($orders as $order) : ?>
                    <?php
                    if ($count > 4) break;
                    $count++
                    ?>
                    <tr>
                      <td><a href="./product-details.html"><?php echo $order->id ?></a></td>
                      <td class="productimgname">
                        <a class="product-img" href="productlist.html">
                          <img src="<?php echo $order->imageUrl ? $order->imageUrl : './assets/img/no-avatar-image.png' ?>" alt="avatar" />
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
                  <?php endforeach; ?>
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
              <table class="table">
                <thead>
                  <tr>
                    <th>Product ID</th>
                    <th>Name</th>
                    <th>Category</th>
                    <th>Created At</th>
                  </tr>
                </thead>
                <tbody>
                  <?php $count = 1 ?>
                  <?php foreach ($products as $product) : ?>
                    <?php
                    if ($count > 4) break;
                    $count++
                    ?>
                    <tr>
                      <td><a href="<?php echo APP_URL . "/admin/product-details.php?id=$product->id" ?>"><?php echo $product->id ?></a></td>
                      <td class="productimgname">
                        <a class="product-img" href="<?php echo APP_URL . "/admin/product-details.php?id=$product->id" ?>">
                          <img src="<?php echo $product->imageUrl ? $product->imageUrl : './assets/img/no-image.png' ?>" alt="product" />
                        </a>
                        <a href="<?php echo APP_URL . "/admin/product-details.php?id=$product->id" ?>"><?php echo $product->name ?></a>
                      </td>
                      <td><?php echo $product->categoryName ?></td>
                      <td><?php echo $product->createdAt ?></td>
                    </tr>
                  <?php endforeach; ?>
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
              <table class="table">
                <thead>
                  <tr>
                    <th>Customer ID</th>
                    <th>Full Name</th>
                    <th>Phone</th>
                    <th>Email</th>
                    <th>Address</th>
                    <th>Created At</th>
                  </tr>
                </thead>
                <tbody>
                  <?php $count = 1 ?>
                  <?php foreach ($customers as $customer) : ?>
                    <?php
                    if ($count > 4) break;
                    $count++
                    ?>
                    <tr>
                      <td><a href="./product-details.html"><?php echo $customer->id ?></a></td>
                      <td class="productimgname">
                        <a class="product-img" href="productlist.html">
                          <img src="<?php echo $customer->imageUrl ? $customer->imageUrl : './assets/img/no-avatar-image.png' ?>" alt="product" />
                        </a>
                        <a href="product-details.html">
                          <?php echo $customer->firstName . ' ' . $customer->lastName ?>
                        </a>
                      </td>
                      <td><?php echo $customer->phoneNumber ?></td>
                      <td><?php echo $customer->email ?></td>
                      <td><?php echo $customer->address ?></td>
                      <td><?php echo $customer->createdAt ?></td>
                    </tr>
                  <?php endforeach; ?>
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
              <table class="table">
                <thead>
                  <tr>
                    <th>User ID</th>
                    <th>Full Name</th>
                    <th>Phone</th>
                    <th>Email</th>
                    <th>Address</th>
                    <th>Created At</th>
                  </tr>
                </thead>
                <tbody>
                  <?php $count = 1 ?>
                  <?php foreach ($users as $user) : ?>
                    <?php
                    if ($count > 4) break;
                    $count++
                    ?>
                    <tr>
                      <td><a href="./product-details.html">
                          <?php echo $user->id ?>
                        </a></td>
                      <td class="productimgname">
                        <a class="product-img" href="productlist.html">
                          <img src="
                            <?php echo $user->imageUrl ? $user->imageUrl : './assets/imag/no-avatar-image.png' ?>
                          " alt="product" />
                        </a>
                        <a href="product-details.html">
                          <?php echo $user->firstName . ' ' . $user->lastName ?>
                        </a>
                      </td>
                      <td><?php echo $user->phoneNumber ?></td>
                      <td><?php echo $user->email ?></td>
                      <td><?php echo $user->address ?></td>
                      <td><?php echo $user->createdAt ?></td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<?php require_once "./inc/components/footer.php" ?>;