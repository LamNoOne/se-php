<?php
require_once  dirname(__DIR__) . "/inc/init.php";
$conn = require_once dirname(__DIR__) . '/inc/db.php';
$categories = Category::getAllCategories($conn);
?>

<?php require_once "./inc/components/header.php" ?>;

<style>
  .modal .modal-content {
    border-radius: 5px;
  }

  .modal .modal-header h1 {
    background: var(--primary-color) !important;
    background-clip: text !important;
    -webkit-text-fill-color: transparent !important;
    font-weight: 700;
    font-size: 25px;
    letter-spacing: 2px;
    text-transform: capitalize;
  }

  .modal .modal-header,
  .modal .modal-body,
  .modal .modal-footer {
    padding-left: 32px;
    padding-right: 32px;
  }
</style>

<div class="page-wrapper">
  <div class="content">
    <div class="page-header">
      <div class="page-title">
        <h3>Product List</h3>
        <h4>Manage your products</h4>
      </div>
      <div class="page-btn">
        <button href="add-product.php" class="btn btn-added box-shadow" data-bs-target="#addProductModal" data-bs-toggle="modal">
          <img src="assets/img/icons/plus.svg" alt="img" class="me-1" />
          Add New Product
        </button>
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
          <!-- <div class="wordset">
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
          </div> -->
        </div>

        <div class="card mb-2" id="filter_inputs">
          <div class="card-body pb-0">
            <div class="row">
              <div class="col-lg-12 col-sm-12">
                <div class="row">
                  <div class="col-lg-2 col-sm-6 col-12">
                    <div class="form-group m-3">
                      <select class="select">
                        <option>Choose Category</option>
                        <option>Computers</option>
                        <option>Fruits</option>
                      </select>
                    </div>
                  </div>
                  <div class="col-lg-2 col-sm-6 col-12">
                    <div class="form-group m-3">
                      <select class="select">
                        <option>Price</option>
                        <option>150.00</option>
                      </select>
                    </div>
                  </div>
                  <div class="col-lg col-sm-6 col-12">
                    <div class="form-group m-3">
                      <a class="btn btn-filters ms-auto"><img src="assets/img/icons/search-whites.svg" alt="img" /></a>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="table-responsive">
          <table class="table datanew" id="table">
            <thead>
              <tr>
                <th>
                  <label class="checkboxs">
                    <input type="checkbox" id="select-all" />
                    <span class="checkmarks"></span>
                  </label>
                </th>
                <th>Name</th>
                <th>Price</th>
                <th>Quantity</th>
                <th>Category</th>
                <th>Created At</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="addProductModal" aria-hidden="true" aria-labelledby="addProductModalLabel" tabindex="-1">
  <div class="modal-dialog modal-xl modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-5" id="addProductModalLabel">Add New Product</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"><i class="fas fa-times"></i></button>
      </div>
      <div class="modal-body">
        <form id="addProductForm" action="add-product.php" method="POST" enctype="multipart/form-data">
          <div class="row gx-5">
            <div class="col-lg-3 col-sm-6 col-12">
              <div class="form-group">
                <label>Product Name</label>
                <input type="text" name="name" autofocus />
              </div>
            </div>
            <div class="col-lg-3 col-sm-6 col-12">
              <div class="form-group">
                <label>Category</label>
                <select name="categoryId" class="select">
                  <?php foreach ($categories as $category) : ?>
                    <option value="<?php echo $category->id ?>">
                      <?php echo $category->name ?>
                    </option>
                  <?php endforeach; ?>
                </select>
              </div>
            </div>
            <div class="col-lg-3 col-sm-6 col-12">
              <div class="form-group">
                <label>Price</label>
                <input type="number" name="price" />
              </div>
            </div>
            <div class="col-lg-3 col-sm-6 col-12">
              <div class="form-group">
                <label>Stock Quantity</label>
                <input type="number" name="stockQuantity" />
              </div>
            </div>
            <div class="col-lg-3 col-sm-6 col-12">
              <div class="form-group">
                <label>Screen</label>
                <input type="text" name="screen" />
              </div>
            </div>
            <div class="col-lg-3 col-sm-6 col-12">
              <div class="form-group">
                <label>Operating System</label>
                <input type="text" name="operatingSystem" />
              </div>
            </div>
            <div class="col-lg-3 col-sm-6 col-12">
              <div class="form-group">
                <label>Processor</label>
                <input type="text" name="processor" />
              </div>
            </div>
            <div class="col-lg-3 col-sm-6 col-12">
              <div class="form-group">
                <label>RAM</label>
                <input type="number" name="ram" />
              </div>
            </div>
            <div class="col-lg-3 col-sm-6 col-12">
              <div class="form-group">
                <label>Storage Capacity</label>
                <input type="number" name="storageCapacity" />
              </div>
            </div>
            <div class="col-lg-3 col-sm-6 col-12">
              <div class="form-group">
                <label>Weight</label>
                <input type="number" name="weight" />
              </div>
            </div>
            <div class="col-lg-3 col-sm-6 col-12">
              <div class="form-group">
                <label>Battery Capacity</label>
                <input type="number" name="batteryCapacity" />
              </div>
            </div>
            <div class="col-lg-3 col-sm-6 col-12">
              <div class="form-group">
                <label>Color</label>
                <input type="text" name="color" />
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
                <div class="preview-image-wrapper mx-auto">
                  <div class="preview-image">
                    <div class="image">
                      <img>
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
                  </div>
                  <button class="choose-file-btn">Choose a image</button>
                </div>
              </div>
            </div>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="submit" class="btn btn-submit me-2">Add</button>
        <button type="reset" class="btn btn-cancel" data-bs-dismiss="modal">Cancel</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="editProductModal" aria-hidden="true" aria-labelledby="editProductModalLabel" tabindex="-1">
  <div class="modal-dialog modal-xl modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-5" id="editProductModalLabel">Edit Product</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"><i class="fas fa-times"></i></button>
      </div>
      <div class="modal-body">
        <form id="editProductForm" action="add-product.php" method="POST" enctype="multipart/form-data">
          <div class="row gx-5">
            <div class="col-lg-3 col-sm-6 col-12">
              <div class="form-group">
                <label>Product Name</label>
                <input type="text" name="name" autofocus />
              </div>
            </div>
            <div class="col-lg-3 col-sm-6 col-12">
              <div class="form-group">
                <label>Category</label>
                <select name="categoryId" class="select">
                  <?php foreach ($categories as $category) : ?>
                    <option value="<?php echo $category->id ?>">
                      <?php echo $category->name ?>
                    </option>
                  <?php endforeach; ?>
                </select>
              </div>
            </div>
            <div class="col-lg-3 col-sm-6 col-12">
              <div class="form-group">
                <label>Price</label>
                <input type="number" name="price" />
              </div>
            </div>
            <div class="col-lg-3 col-sm-6 col-12">
              <div class="form-group">
                <label>Stock Quantity</label>
                <input type="number" name="stockQuantity" />
              </div>
            </div>
            <div class="col-lg-3 col-sm-6 col-12">
              <div class="form-group">
                <label>Screen</label>
                <input type="text" name="screen" />
              </div>
            </div>
            <div class="col-lg-3 col-sm-6 col-12">
              <div class="form-group">
                <label>Operating System</label>
                <input type="text" name="operatingSystem" />
              </div>
            </div>
            <div class="col-lg-3 col-sm-6 col-12">
              <div class="form-group">
                <label>Processor</label>
                <input type="text" name="processor" />
              </div>
            </div>
            <div class="col-lg-3 col-sm-6 col-12">
              <div class="form-group">
                <label>RAM</label>
                <input type="number" name="ram" />
              </div>
            </div>
            <div class="col-lg-3 col-sm-6 col-12">
              <div class="form-group">
                <label>Storage Capacity</label>
                <input type="number" name="storageCapacity" />
              </div>
            </div>
            <div class="col-lg-3 col-sm-6 col-12">
              <div class="form-group">
                <label>Weight</label>
                <input type="number" name="weight" />
              </div>
            </div>
            <div class="col-lg-3 col-sm-6 col-12">
              <div class="form-group">
                <label>Battery Capacity</label>
                <input type="number" name="batteryCapacity" />
              </div>
            </div>
            <div class="col-lg-3 col-sm-6 col-12">
              <div class="form-group">
                <label>Color</label>
                <input type="text" name="color" />
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
                <div class="preview-image-wrapper mx-auto">
                  <div class="preview-image">
                    <div class="image">
                      <img>
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
                    <input name="currentImageUrl" class="current-image-url" type="hidden">
                  </div>
                  <button class="choose-file-btn">Choose a image</button>
                </div>
              </div>
            </div>
            <div class="col-lg-12 mt-5">
              <button type="submit" class="btn btn-submit me-2">Update</button>
              <button type="reset" class="btn btn-cancel" data-bs-dismiss="modal">Cancel</button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<?php require_once "./inc/components/footer.php" ?>;

<script>
  $(document).ready(function() {
    const DEFAULT_PAGE = 1
    const DEFAULT_LIMIT = 10
    const DEFAULT_SEARCH = ''
    const DEFAULT_SORT_BY = 'createdAt'
    const DEFAULT_ORDER = 'asc'

    // handle render products to table
    const table = $('#table').DataTable({
      processing: true,
      serverSide: true,
      bFilter: true,
      sDom: 'fBtlpi',
      pagingType: 'numbers',
      ordering: true,
      lengthMenu: [
        [10, 25, 50, -1],
        [10, 25, 50, 'All']
      ],
      language: {
        search: '',
        sLengthMenu: '_MENU_',
        searchPlaceholder: 'Search...',
        info: '_START_ - _END_ of _TOTAL_ items'
      },
      order: [
        [5, 'asc']
      ],
      ajax: {
        url: 'actions/get-products.php',
        type: 'GET',
        data: function(d, settings) {
          return {
            page: d.start / d.length + 1,
            limit: d.length,
            search: d.search?.value,
            sortBy: d.columns[d.order[0]?.column]?.name || 'createdAt',
            order: d.order[0]?.dir || 'asc',
            draw: d.draw
          }
        },
        dataFilter: function(data) {
          const dataObj = jQuery.parseJSON(data);
          return JSON.stringify({
            draw: dataObj.draw,
            recordsTotal: dataObj.totalItems,
            recordsFiltered: dataObj.totalItems,
            data: dataObj.items,
            totalPages: dataObj.totalPages
          });
        },
      },
      columnDefs: [{
          targets: 0,
          orderable: false,
          searchable: false,
        },
        {
          name: 'name',
          targets: 1
        },
        {
          name: 'price',
          targets: 2
        },
        {
          name: 'stockQuantity',
          targets: 3
        },
        {
          name: 'categoryId',
          targets: 4
        },
        {
          name: 'createdAt',
          targets: 5
        },
        {
          targets: 6,
          orderable: false,
          searchable: false,
        },
      ],
      columns: [{
          render: function(data, type, row, meta) {
            return `
                <label class="checkboxs">
                  <input data-id=${row.id} type="checkbox" />
                  <span class="checkmarks"></span>
                </label>
              `
          }
        },
        {
          render: function(data, type, row, meta) {
            return `
              <a href="product-details.php?id=${row.id}" class="product-img">
                <img src="${row.imageUrl}" />
              </a>
              <a class="text-linear-hover" href="product-details.php?id=${row.id}">
                ${row.name}
              </a>
            `
          }
        },
        {
          data: 'price'
        },
        {
          data: 'stockQuantity'
        },
        {
          data: 'categoryName'
        },
        {
          data: 'createdAt'
        },
        {
          render: function(data, type, row, meta) {
            return `
              <a class="me-3" href="product-details.php?id=${row.id}">
                <img src="assets/img/icons/eye.svg" alt="img" />
              </a>
              <a
                class="me-3 edit-product-button"
                data-id="${row.id}"
                href="javascript:void(0)"
              >
                <img src="assets/img/icons/edit.svg" alt="img" />
              </a>
              <a data-id="${row.id}" id="delete-btn" href="javascript:void(0)">
                <img src="assets/img/icons/delete.svg" alt="img" />
              </a>
              `
          }
        },
      ],
      initComplete: (settings, json) => {
        $('.dataTables_filter').appendTo('#tableSearch')
        $('.dataTables_filter').appendTo('.search-input')
      },
    })

    // handle add product
    const addProductFormId = '#addProductForm'
    const addProductModalId = '#addProductModal'
    const addProductForm = $(addProductFormId)
    const addProductModal = $(addProductModalId)
    const productFormSubmitButton = $(addProductModalId + ' .modal-footer button[type="submit"]')
    addProductForm.validate({
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
    productFormSubmitButton.click(function() {
      addProductForm.submit()
    })
    addProductForm.submit(async function(event) {
      const clearForm = () => {
        $(addProductModalId).modal('hide');
        $(this).closest(addProductFormId).find('input, textarea, select').val('')
        $(this).closest(addProductFormId).find('select').prop('selectedIndex', 0)
        $(this).closest(addProductFormId).find('.preview-image img').prop('src', '').hide();
      }
      try {
        event.preventDefault()
        if ($(this).valid()) {
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
            toastr.success(response.message)
            table.ajax.reload(function(json) {
              // Fix bug: put in setTimeout => added item and move last page
              // but records are still at page = 1, limit = 10
              // Ref: https://datatables.net/forums/discussion/31857/page-draw-is-not-refreshing-the-rows-on-the-table
              setTimeout(function() {
                table.page(json.totalPages - 1).draw('page');
              }, 0);
            });
          } else {
            toastr.error(response.message)
          }
          clearForm();
        }
      } catch (error) {
        clearForm();
        toastr.error('Something went wrong')
      }
    })

    // handle edit product
    $('#table tbody').on('click', '.edit-product-button', async function(event) {
      try {
        const id = $(this).data('id')
        const response = await $.ajax({
          url: `actions/get-product-by-id.php?id=${id}`,
          type: 'GET',
          dataType: 'json'
        })
        if (response.status) {
          const modal = bootstrap.Modal.getOrCreateInstance(document.getElementById('editProductModal'))
          modal.show();
          console.log(response);
          const product = response.data.product;

          const editProductFormId = '#editProductForm'
          $(`${editProductFormId} input[name="name"]`).val(product.name)
          $(`${editProductFormId} input[name="price"]`).val(product.price)
          $(`${editProductFormId} input[name="stockQuantity"]`).val(product.stockQuantity)
          $(`${editProductFormId} input[name="screen"]`).val(product.screen)
          $(`${editProductFormId} input[name="operatingSystem"]`).val(product.operatingSystem)
          $(`${editProductFormId} input[name="processor"]`).val(product.processor)
          $(`${editProductFormId} input[name="ram"]`).val(product.ram)
          $(`${editProductFormId} input[name="storageCapacity"]`).val(product.storageCapacity)
          $(`${editProductFormId} input[name="weight"]`).val(product.weight)
          $(`${editProductFormId} input[name="batteryCapacity"]`).val(product.batteryCapacity)
          $(`${editProductFormId} input[name="color"]`).val(product.color)
          $(`${editProductFormId} textarea[name="description"]`).val(product.description)
          $(`${editProductFormId} .preview-image img`).attr('src', product.imageUrl).show()
          $(`${editProductFormId} .preview-image`).css({
            'border': 'none'
          })
          $(`${editProductFormId} input[name="currentImageUrl"]`).val(product.imageUrl)

        } else {
          toastr.error('Something went wrong')
        }
      } catch (error) {
        toastr.error('Something went wrong')
      }
    })
    $('#editProductForm').validate({
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
    $('#editProductForm').submit(async function(event) {
      const clearForm = () => {
        $('#editProductModal').modal('hide');
        $(this).closest('#editProductForm').find('input, textarea, select').val('')
        $(this).closest('#editProductForm').find('select').prop('selectedIndex', 0)
        $(this).closest('#editProductForm').find('.preview-image img').prop('src', '').hide();
      }
      try {
        event.preventDefault()
        if ($(this).valid()) {
          const formData = new FormData($(this)[0])

          const response = await $.ajax({
            url: 'actions/edit-product.php',
            type: 'POST',
            dataType: 'json',
            data: formData,
            contentType: false,
            processData: false,
          })
          if (response.status) {
            toastr.success(response.message)
            table.page('last').draw('page')
          } else {
            toastr.error(response.message)
          }
          clearForm();
        }
      } catch (error) {
        clearForm();
        toastr.error('Something went wrong')
      }
    })

    // handle delete product
    $('#table tbody').on('click', '#delete-btn', function() {
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