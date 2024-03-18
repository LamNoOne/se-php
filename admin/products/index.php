<?php
require_once  dirname(dirname(__DIR__)) . "/inc/init.php";
$conn = require_once  dirname(dirname(__DIR__)) . "/inc/db.php";

Auth::requireLogin();
Auth::requireAdmin($conn);

?>

<?php require_once  dirname(__DIR__) . "/inc/components/header.php" ?>;

<div class="page-wrapper">
  <div class="content">
    <div class="page-header">
      <div class="page-title">
        <h3>Product List</h3>
        <h4>Manage your products</h4>
      </div>
      <div id="openProductModalButton" class="page-btn">
        <button class="btn btn-added box-shadow">
          <img src="<?php echo APP_URL; ?>/admin/assets/img/icons/plus.svg" alt="img" class="me-1" />
          Add New Product
        </button>
      </div>
    </div>

    <div class="card">
      <div class="card-body">
        <div class="table-top">
          <div class="search-set">
            <div class="search-path">
              <a class="btn btn-danger btn-delete-by-select" id="deleteBySelectBtn">
                <i class="fas fa-trash-alt"></i>
              </a>
            </div>
            <div class="search-input">
              <a class="btn btn-searchset">
                <i class="fas fa-search"></i>
              </a>
            </div>
          </div>
        </div>

        <div class="table-responsive">
          <table class="table" id="table">
            <thead class="table-light">
              <tr>
                <th>
                  <label class="checkboxs">
                    <input type="checkbox" id="select-all" />
                    <span class="checkmarks"></span>
                  </label>
                </th>
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
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="addModal" aria-hidden="true" aria-labelledby="addModalLabel" tabindex="-1">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-5" id="addModalLabel">Add New Product</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"><i class="fas fa-times"></i></button>
      </div>
      <div class="modal-body">
        <form id="addForm" enctype="multipart/form-data">
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

<div class="modal fade" id="editModal" aria-hidden="true" aria-labelledby="editModalLabel" tabindex="-1">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-5" id="editModalLabel">Edit Product</h1>
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
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="submit" class="btn btn-submit me-2">Update</button>
        <button type="reset" class="btn btn-cancel" data-bs-dismiss="modal">Cancel</button>
      </div>
    </div>
  </div>
</div>

<?php require_once dirname(__DIR__) . "/inc/components/footer.php" ?>;

<script>
  $(document).ready(function() {
    const DEFAULT_PAGE = 1
    const DEFAULT_LIMIT = 10
    const DEFAULT_SEARCH = ''
    const DEFAULT_SORT_BY = 'createdAt'
    const DEFAULT_ORDER = 'asc'
    const tableEle = $('#table')

    const clearForm = (form) => {
      const previewImage = form.find('.preview-image')
      const fileName = previewImage.find('.file-name')
      form.find('input, textarea, select').val('')
      form.find('.preview-image img').prop('src', '').hide();
      form.find('select').html('')
      previewImage.removeClass('active');
      fileName.text('');
    }

    const goToCurrentPage = (table = {}, isDeleteItem = false, oldPageInfo = null) => {
      let pageInfo = table.page.info()
      if (isDeleteItem && oldPageInfo) {
        pageInfo = oldPageInfo
      }
      const numberItemsBefore = pageInfo.end - pageInfo.start
      let currentPage = pageInfo.page
      if (isDeleteItem && numberItemsBefore === 1 && currentPage > 0) {
        currentPage = currentPage - 1;
      }

      // Fix bug: put in setTimeout => added item and move last page
      // but records are still at page = 1, limit = 10
      // Ref: https://datatables.net/forums/discussion/31857/page-draw-is-not-refreshing-the-rows-on-the-table
      setTimeout(() => {
        table.page(currentPage).draw('page')
      }, 0)
    }

    const goToLastPage = (table = {}, isAddItem = false) => {
      const pageInfo = table.page.info()
      let totalPages = pageInfo.pages;
      if (isAddItem && ((pageInfo.end - pageInfo.start) === pageInfo.length)) {
        totalPages = pageInfo.pages + 1;
      }

      // Fix bug: put in setTimeout => added item and move last page
      // but records are still at page = 1, limit = 10
      // Ref: https://datatables.net/forums/discussion/31857/page-draw-is-not-refreshing-the-rows-on-the-table
      setTimeout(() => {
        table.page(totalPages - 1).draw('page')
      }, 0)
    }

    // handle render items to table
    const table = tableEle.DataTable({
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
        [6, 'asc']
      ],
      ajax: {
        url: '<?php echo GET_PRODUCTS_API; ?>',
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
            draw: dataObj.data.draw,
            recordsTotal: dataObj.data.totalItems,
            recordsFiltered: dataObj.data.totalItems,
            data: dataObj.data.items,
            totalPages: dataObj.data.totalPages
          });
        },
      },
      columnDefs: [{
          targets: 0,
          orderable: false,
          searchable: false,
        },
        {
          name: 'id',
          targets: 1
        },
        {
          name: 'name',
          targets: 2
        },
        {
          name: 'price',
          targets: 3
        },
        {
          name: 'stockQuantity',
          targets: 4
        },
        {
          name: 'categoryId',
          targets: 5
        },
        {
          name: 'createdAt',
          targets: 6
        },
        {
          targets: 7,
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
              <a class="text-linear-hover details-btn" href="<?php echo APP_URL; ?>/admin/products/details.php?id=${row.id}">
                ${row.id}
              </a>
            `
          }
        },
        {
          render: function(data, type, row, meta) {
            return `
              <div class="name-img-wrapper">
                <a class="product-img details-btn" href="<?php echo APP_URL; ?>/admin/products/details.php?id=${row.id}" class="product-img">
                  <img src="${row.imageUrl}" />
                </a>
                <a class="text-linear-hover details-btn" href="<?php echo APP_URL; ?>/admin/products/details.php?id=${row.id}">
                  ${row.name}
                </a>
              </div>
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
              <a class="me-2 action details-btn" href="<?php echo APP_URL; ?>/admin/products/details.php?id=${row.id}">
                <img class="action-icon" src="<?php echo APP_URL; ?>/admin/assets/img/icons/eye.svg" alt="img" />
              </a>
              <a
                class="me-2 edit-button action"
                data-id="${row.id}"
                href="javascript:void(0)"
              >
                <img class="action-icon" src="<?php echo APP_URL; ?>/admin/assets/img/icons/edit.svg" alt="img" />
              </a>
              <a class="action" data-id="${row.id}" id="delete-btn" href="javascript:void(0)">
                <img class="action-icon" src="<?php echo APP_URL; ?>/admin/assets/img/icons/delete.svg" alt="img" />
              </a>
              `
          }
        },
      ],
      initComplete: (settings, json) => {
        $('.dataTables_filter').appendTo('#tableSearch')
        $('.dataTables_filter').appendTo('.search-input')

        // In order to go to current page of deleted item
        if (sessionStorage.getItem('pageInfo')) {
          const pageInfo = JSON.parse(sessionStorage.getItem('pageInfo'));
          sessionStorage.removeItem('pageInfo');
          goToCurrentPage(table, pageInfo)
        }
      }
    })

    // handle add
    const addFormId = '#addForm'
    const addModalId = '#addModal'
    const addForm = $(addFormId)
    const addModal = $(addModalId)
    const addModalBootstrapInstance = bootstrap.Modal.getOrCreateInstance(document.getElementById('addModal'))
    const addFormSubmitButton = $(addModalId + ' .modal-footer button[type="submit"]')
    // handle clear category select when hidden add modal
    addModal.on("hidden.bs.modal", function() {
      clearForm(addForm);
    });
    $('#openProductModalButton').click(async function() {
      try {
        const response = await $.ajax({
          url: '<?php echo GET_CATEGORIES_API; ?>',
          type: 'GET',
          dataType: 'json',
        })
        if (response.status) {
          const categories = response.data.items
          const categorySelect = addForm.find('select[name="categoryId"]')
          categorySelect.append(`
            <option value="" selected>Choose Category</option>
          `)
          categories.forEach((category, index) => {
            categorySelect.append(`
              <option value="${category.id}">
                ${category.name}
              </option>
            `)
          })
          addModalBootstrapInstance.show()
        } else {
          toastr.error('Something went wrong')
        }
      } catch (error) {
        toastr.error('Something went wrong')
      }
    });
    addFormSubmitButton.click(function() {
      addForm.submit()
    })
    addForm.validate({
      rules: {
        name: {
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
    addForm.submit(async function(event) {
      try {
        event.preventDefault()
        if ($(this).valid()) {
          const formData = new FormData($(this)[0])
          const response = await $.ajax({
            url: '<?php echo ADD_PRODUCT_API; ?>',
            type: 'POST',
            dataType: 'json',
            data: formData,
            contentType: false,
            processData: false,
          })
          if (response.status) {
            goToLastPage(table, true)
            toastr.success('Add product successfully')
          } else {
            toastr.error(response.message)
          }
          addModalBootstrapInstance.hide()
        }
      } catch (error) {
        addModalBootstrapInstance.hide()
        toastr.error('Something went wrong')
      }
    })

    // handle edit
    const editProductFormId = '#editProductForm'
    const editModalId = '#editModal'
    const editProductForm = $(editProductFormId)
    const editModal = $(editModalId)
    const editModalBootstrapInstance = bootstrap.Modal.getOrCreateInstance(document.getElementById('editModal'))
    const editProductFormSubmitButton = $(editModalId + ' .modal-footer button[type="submit"]')
    editModal.on("hidden.bs.modal", function() {
      clearForm(editProductForm);
    });
    $('#table tbody').on('click', '.edit-button', async function(event) {
      try {
        const id = $(this).data('id')
        const getProduct = $.ajax({
          url: `<?php echo GET_PRODUCT_BY_ID_API; ?>?id=${id}`,
          type: 'GET',
          dataType: 'json'
        })
        const getCategories = $.ajax({
          url: `<?php echo GET_CATEGORIES_API; ?>`,
          type: 'GET',
          dataType: 'json'
        })
        const [getProductResponse, getCategoriesResponse] = await Promise.all([
          getProduct, getCategories
        ])

        if (getProductResponse.status && getCategoriesResponse.status) {
          const product = getProductResponse.data.product;
          const categories = getCategoriesResponse.data.items
          const categorySelect = editProductForm.find('select[name="categoryId"]')

          editProductForm.attr('data-id', product.id)
          editProductForm.find('input[name="name"]').val(product.name)
          editProductForm.find('input[name="price"]').val(product.price)
          editProductForm.find('input[name="stockQuantity"]').val(product.stockQuantity)
          editProductForm.find('input[name="screen"]').val(product.screen)
          editProductForm.find('input[name="operatingSystem"]').val(product.operatingSystem)
          editProductForm.find('input[name="processor"]').val(product.processor)
          editProductForm.find('input[name="ram"]').val(product.ram)
          editProductForm.find('input[name="storageCapacity"]').val(product.storageCapacity)
          editProductForm.find('input[name="weight"]').val(product.weight)
          editProductForm.find('input[name="batteryCapacity"]').val(product.batteryCapacity)
          editProductForm.find('input[name="color"]').val(product.color)
          editProductForm.find('textarea[name="description"]').val(product.description)
          editProductForm.find('.preview-image img').attr('src', product.imageUrl).show()
          editProductForm.find('.preview-image').css({
            'border': 'none'
          })
          editProductForm.find('input[name="currentImageUrl"]').val(product.imageUrl)
          categorySelect.append(`
            <option value="" selected>Choose Category</option>
          `)
          categories.forEach((category) => {
            let selectedAttr = '';
            if (category.id === product.categoryId) {
              selectedAttr = 'selected'
            }
            categorySelect.append(`
              <option value="${category.id}" ${selectedAttr}>
                ${category.name}
              </option>
            `)
          })

          editModalBootstrapInstance.show()
        } else {
          toastr.error('Something went wrong')
        }
      } catch (error) {
        toastr.error('Something went wrong')
      }
    })
    editProductFormSubmitButton.click(function() {
      editProductForm.submit()
    })
    editProductForm.validate({
      rules: {
        name: {
          required: true
        },
        categoryId: {
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
    editProductForm.submit(async function(event) {
      try {
        event.preventDefault()
        if ($(this).valid()) {
          const id = $(this).data('id')
          const formData = new FormData($(this)[0])
          formData.append('id', id)

          const response = await $.ajax({
            url: '<?php echo EDIT_PRODUCT_API; ?>',
            type: 'POST',
            dataType: 'json',
            data: formData,
            contentType: false,
            processData: false,
          })
          if (response.status) {
            const currentPage = table.page.info().page;
            table.page(currentPage).draw('page')
            toastr.success('Edit product successfully')
          } else {
            toastr.error('Edit product failed')
          }
          editModalBootstrapInstance.hide()
        }
      } catch (error) {
        editModalBootstrapInstance.hide()
        toastr.error('Something went wrong')
      }
    })

    // handle delete a item
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
                url: '<?php echo DELETE_PRODUCT_BY_ID_API; ?>',
                type: 'POST',
                dataType: 'json',
                data: {
                  id
                },
              })

              if (response.status) {
                const pageInfo = table.page.info()
                goToCurrentPage(table, pageInfo);
                toastr.success(response.message)
              } else {
                toastr.error(response.message)
              }
            }
          } catch (error) {
            toastr.error('Something went wrong')
          }
        })
    })

    // handle delete by select
    $('#deleteBySelectBtn').click(function() {
      const selectAll = tableEle.find('#select-all')
      const checkedBoxes = tableEle.find(
        'input[type="checkbox"]:checked:not([id="select-all"])'
      )
      let checkedIds = [];
      checkedBoxes.each(function() {
        checkedIds = [...checkedIds, $(this).data('id')]
      })

      Swal
        .fire({
          title: 'Delete Selected Products?',
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
                url: '<?php echo DELETE_PRODUCT_BY_IDS_API; ?>',
                type: 'POST',
                dataType: 'json',
                data: {
                  ids: checkedIds
                },
              })

              if (response.status) {
                const currentPage = table.page.info().page
                const lastPage = table.page.info().pages
                let pageAfterDelete = currentPage
                const isAtLastPage = currentPage === lastPage - 1;
                if (selectAll.is(':checked') && isAtLastPage) {
                  pageAfterDelete = currentPage - 1;
                }
                setTimeout(() => {
                  table.page(pageAfterDelete).draw('page')
                })
                toastr.success(response.message)
              } else {
                toastr.error(response.message)
              }
            }
          } catch (error) {
            toastr.error('Something went wrong')
          }
        })
    })

    // In order to go to current page of deleted item
    $('#table tbody').on('click', '.details-btn', function() {
      sessionStorage.setItem('pageInfo', JSON.stringify(table.page.info()))
    })
  })
</script>