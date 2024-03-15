<?php
require_once  dirname(__DIR__) . "/inc/init.php";
if (!isset($conn))
  $conn = require_once dirname(__DIR__) . "/inc/db.php";

$orders = [];
$customers = [];
$users = [];
$products = [];
$totalOrders = 0;
$totalProducts = 0;
$totalCustomers = 0;
$totalUsers = 0;

try {
  $pagination = ['limit' => 4, 'offset' => 0];
  $sort = [['sortBy' => 'createdAt', 'order' => 'DESC']];

  $getOrdersResult = Order::getOrders(
    $conn,
    [],
    $pagination,
    ['sortBy' => 'createdAt', 'order' => 'DESC']
  );
  $getCustomersResult = User::getUsers(
    $conn,
    [['field' => 'roleId', 'value' => CUSTOMER]],
    $pagination,
    $sort
  );
  $getUsersResult = User::getUsers(
    $conn,
    [],
    $pagination,
    $sort
  );
  $products = Product::getAllProductsForAdmin(
    $conn,
    [],
    $pagination,
    ['sortBy' => 'createdAt', 'order' => 'DESC']
  );
  if ($getOrdersResult['status']) {
    $orders = $getOrdersResult['data']['orders'];
  }
  if ($getCustomersResult['status']) {
    $customers = $getCustomersResult['data']['users'];
  }
  if ($getUsersResult['status']) {
    $users = $getUsersResult['data']['users'];
  }

  $countOrdersResult = Order::count($conn, []);
  $countProductsResult = Product::count($conn, []);
  $countCustomersResult = User::count($conn, [['field' => 'roleId', 'value' => CUSTOMER]]);
  $countUsersResult = User::count($conn, []);
  if ($countOrdersResult['status']) {
    $totalOrders = $countOrdersResult['data']['total'];
  }
  if ($countProductsResult['status']) {
    $totalProducts = $countProductsResult['data']['total'];
  }
  if ($countCustomersResult['status']) {
    $totalCustomers = $countCustomersResult['data']['total'];
  }
  if ($countUsersResult['status']) {
    $totalUsers = $countUsersResult['data']['total'];
  }
} catch (Exception $e) {
}

?>

<?php require_once "./inc/components/header.php" ?>;

<div class="page-wrapper">
  <div class="content">
    <div class="row">
      <div class="col-lg-3 col-sm-6 col-12 d-flex">
        <div class="dash-count box-shadow">
          <div class="dash-counts">
            <h4><?php echo $totalOrders ?></h4>
            <h5>Orders</h5>
          </div>
          <div class="dash-imgs">
            <i data-feather="file-text"></i>
          </div>
        </div>
      </div>
      <div class="col-lg-3 col-sm-6 col-12 d-flex">
        <div class="dash-count das1 box-shadow">
          <div class="dash-counts">
            <h4><?php echo $totalProducts ?></h4>
            <h5>Products</h5>
          </div>
          <div class="dash-imgs">
            <i class="fas fa-mobile-alt"></i>
          </div>
        </div>
      </div>
      <div class="col-lg-3 col-sm-6 col-12 d-flex">
        <div class="dash-count das2 box-shadow">
          <div class="dash-counts">
            <h4><?php echo $totalCustomers ?></h4>
            <h5>Customers</h5>
          </div>
          <div class="dash-imgs">
            <i data-feather="user-check"></i>
          </div>
        </div>
      </div>
      <div class="col-lg-3 col-sm-6 col-12 d-flex">
        <div class="dash-count das3 box-shadow">
          <div class="dash-counts">
            <h4><?php echo $totalUsers ?></h4>
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
                    <th>ID</th>
                    <th>Customer</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Created At</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($orders as $order) : ?>
                    <tr>
                      <td>
                        <a class="text-linear-hover" href="<?php echo APP_URL . "/admin/orders/details?id=$order->id"; ?>">
                          <?php echo $order->id ?>
                        </a>
                      </td>
                      <td>
                        <div class="name-img-wrapper">
                          <a class="product-img" href="<?php echo APP_URL . "/admin/customers/details?id=$order->customerId"; ?>" />
                          <img src="<?php echo $order->customerImageUrl ? $order->customerImageUrl : APP_URL . '/admin/assets/img/no-avatar-image.png' ?>" alt="" />
                          </a>
                          <a class="text-linear-hover" href="<?php echo APP_URL . "/admin/customers/details?id=$order->customerId"; ?>">
                            <?php
                            echo $order->customerFirstName . ' ' . $order->customerLastName
                            ?>
                          </a>
                        </div>
                      </td>
                      <td><?php echo $order->totalPrice ?></td>
                      <td>
                        <span class="badges
                          <?php echo $order->statusId === PENDING ? 'bg-lightred' : 'bg-lightgreen'; ?>">
                          <?php echo $order->statusName ?>
                        </span>
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
                    <th>ID</th>
                    <th>Name</th>
                    <th>Price</th>
                    <th>Stock Quantity</th>
                    <th>Category</th>
                    <th>Created At</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($products as $product) : ?>
                    <tr>
                      <td>
                        <a class="text-linear-hover" href="<?php echo APP_URL . "/admin/products/details.php?id=$product->id" ?>">
                          <?php echo $product->id ?>
                        </a>
                      </td>
                      <td>
                        <div class="name-img-wrapper">
                          <a class="product-img" href="<?php echo APP_URL . "/admin/products/details.php?id=$product->id" ?>">
                            <img src="<?php echo $product->imageUrl ? $product->imageUrl : APP_URL . '/admin/assets/img/no-image.png' ?>" />
                          </a>
                          <a class="text-linear-hover" href="<?php echo APP_URL . "/admin/products/details.php?id=$product->id" ?>"><?php echo $product->name ?></a>
                        </div>
                      </td>
                      <td><?php echo $product->price ?></td>
                      <td><?php echo $product->stockQuantity ?></td>
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
                    <th>ID</th>
                    <th>Full Name</th>
                    <th>Phone</th>
                    <th>Email</th>
                    <th>Address</th>
                    <th>Status</th>
                    <th>Created At</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($customers as $customer) : ?>
                    <tr>
                      <td>
                        <a class="text-linear-hover" href="<?php echo APP_URL . "/admin/customers/details.php?id=$customer->id" ?>"><?php echo $customer->id ?>
                        </a>
                      </td>
                      <td>
                        <div class="name-img-wrapper">
                          <a class="product-img" href="<?php echo APP_URL . "/admin/customers/details.php?id=$customer->id" ?>">
                            <img src="<?php echo $customer->imageUrl ? $customer->imageUrl : APP_URL . '/admin/assets/img/no-avatar-image.png' ?>" />
                          </a>
                          <a class="text-linear-hover" href="<?php echo APP_URL . "/admin/customers/details.php?id=$customer->id" ?>">
                            <?php echo $customer->firstName . ' ' . $customer->lastName ?>
                          </a>
                        </div>
                      </td>
                      <td><?php echo $customer->phoneNumber ?></td>
                      <td><?php echo $customer->email ?></td>
                      <td><?php echo $customer->address ?></td>
                      <td>
                        <span class="badges
                          <?php echo $customer->active ? 'bg-lightgreen' : 'bg-lightred'; ?>">
                          <?php echo $customer->active ? 'Active' : 'Disabled'; ?>
                        </span>
                      </td>
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
                    <th>ID</th>
                    <th>Full Name</th>
                    <th>Phone</th>
                    <th>Email</th>
                    <th>Address</th>
                    <th>Status</th>
                    <th>Created At</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($users as $user) : ?>
                    <tr>
                      <td><a class="text-linear-hover" href="<?php echo APP_URL . "/admin/users/details.php?id=$user->id" ?>">
                          <?php echo $user->id ?>
                        </a></td>
                      <td>
                        <div class="name-img-wrapper">
                          <a class="product-img" href="<?php echo APP_URL . "/admin/users/details.php?id=$user->id" ?>">
                            <img src="
                              <?php echo $user->imageUrl ? $user->imageUrl
                                : APP_URL  . '/admin/assets/imag/no-avatar-image.png'
                              ?>
                            " />
                          </a>
                          <a class="text-linear-hover" href="<?php echo APP_URL . "/admin/users/details.php?id=$user->id" ?>">
                            <?php echo $user->firstName . ' ' . $user->lastName ?>
                          </a>
                        </div>
                      </td>
                      <td><?php echo $user->phoneNumber ?></td>
                      <td><?php echo $user->email ?></td>
                      <td><?php echo $user->address ?></td>
                      <td>
                        <span class="badges
                            <?php echo $user->active ? 'bg-lightgreen' : 'bg-lightred'; ?>">
                          <?php echo $user->active ? 'Active' : 'Disabled'; ?>
                        </span>
                      </td>
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