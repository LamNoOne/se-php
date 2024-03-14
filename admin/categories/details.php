<?php
require_once  dirname(dirname(__DIR__)) . "/inc/init.php";
if (!isset($conn))
  $conn = require_once dirname(dirname(__DIR__)) . "/inc/db.php";

if (!isset($_GET['id'])) {
  header('Location: ' . APP_URL . '/admin/categories');
  return;
}

$id = $_GET['id'];

$result = Category::getCategoryById($conn, $id);

if (!$result['status']) {
  header('Location: ' . APP_URL . '/admin/500.php');
  return;
}

$category = $result['data']['category'];

if (!$category) {
  header('Location: ' . APP_URL . '/admin/404.php');
  return;
}

?>

<?php require_once dirname(__DIR__)  . "/inc/components/header.php" ?>;

<div class="page-wrapper">
  <div class="content">
    <div class="page-header">
      <div class="page-title">
        <h3>Category Details</h3>
        <h4>Full details of a category</h4>
      </div>
      <div class="page-btn">
        <a data-id="<?php echo $category->id ?>" id="delete-btn" class="btn btn-danger" href="javascript:void(0)">Delete</a>
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
                  <h6><?php echo $category->id ?></h6>
                </li>
                <li>
                  <h4>Name</h4>
                  <h6><?php echo $category->name ?></h6>
                </li>
                <li>
                  <h4>Description</h4>
                  <h6><?php echo $category->description ?></h6>
                </li>
              </ul>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="d-flex gap-3">
      <a class="btn btn-primary" href="<?php echo APP_URL; ?>/admin/categories">Back</a>
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
          title: 'Delete Product?',
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