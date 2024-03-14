<?php
require_once  dirname(dirname(__DIR__)) . "/inc/init.php";
?>

<?php require_once  dirname(__DIR__) . "/inc/components/header.php" ?>;

<div class="page-wrapper">
  <div class="content">
    <div class="page-header">
      <div class="page-title">
        <h3>Category List</h3>
        <h4>Manage your categories</h4>
      </div>
      <div class="page-btn" data-bs-toggle="modal" data-bs-target="#addModal">
        <button class="btn btn-added box-shadow">
          <img src="<?php echo APP_URL; ?>/admin/assets/img/icons/plus.svg" alt="img" class="me-1" />
          Add New Category
        </button>
      </div>
    </div>

    <div class="card">
      <div class="card-body">
        <div class="table-top">
          <div class="search-set">
            <div class="search-path">
              <a class="btn btn-filter" id="filter_search">
                <img src="<?php echo APP_URL; ?>/admin/assets/img/icons/filter.svg" alt="img" />
                <span><img src="<?php echo APP_URL; ?>/admin/assets/img/icons/closes.svg" alt="img" /></span>
              </a>
            </div>
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
                      <a class="btn btn-filters ms-auto"><img src="<?php echo APP_URL; ?>/admin/assets/img/icons/search-whites.svg" alt="img" /></a>
                    </div>
                  </div>
                </div>
              </div>
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
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-5" id="addModalLabel">Add New Category</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"><i class="fas fa-times"></i></button>
      </div>
      <div class="modal-body">
        <form id="addForm">
          <div class="row gx-5">
            <div class="col-lg-12 col-sm-12 col-12">
              <div class="form-group">
                <label>Category Name</label>
                <input type="text" name="name" autofocus />
              </div>
            </div>
            <div class="col-lg-12 col-sm-12 col-12">
              <div class="form-group">
                <label>Description</label>
                <textarea class="form-control" name="description"></textarea>
              </div>
            </div>
          </div>
        </form>
      </div>
      <div class="modal-footer d-flex justify-content-end">
        <button type="submit" class="btn btn-submit me-2">Add</button>
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

    const clearForm = (modal, form) => {
      modal.modal('hide');
      form.find('input, textarea').val('')
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
        [3, 'asc']
      ],
      ajax: {
        url: '<?php echo GET_CATEGORIES_API; ?>',
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
          name: 'createdAt',
          targets: 3
        },
        {
          targets: 4,
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
              <a class="text-linear-hover" href="product-details.php?id=${row.id}">
                ${row.id}
              </a>
            `
          }
        },
        {
          render: function(data, type, row, meta) {
            return `
              <a class="text-linear-hover" href="product-details.php?id=${row.id}">
                ${row.name}
              </a>
            `
          }
        },
        {
          data: 'createdAt'
        },
        {
          render: function(data, type, row, meta) {
            return `
              <a class="me-2 action" href="<?php echo APP_URL; ?>/admin/categories/details.php?id=${row.id}">
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
      },
    })

    // handle add item
    const addFormId = '#addForm'
    const addModalId = '#addModal'
    const addForm = $(addFormId)
    const addModal = $(addModalId)
    const addFormSubmitButton = $(addModalId + ' .modal-footer button[type="submit"]')
    addFormSubmitButton.click(function() {
      addForm.submit()
    })
    addForm.validate({
      rules: {
        name: {
          required: true
        }
      },
    })
    addForm.submit(async function(event) {
      try {
        event.preventDefault()
        if ($(this).valid()) {
          const data = addForm.serializeArray().reduce((acc, item) => {
            return {
              ...acc,
              [item.name]: item.value
            }
          }, {})

          const response = await $.ajax({
            url: '<?php echo ADD_CATEGORY_API; ?>',
            type: 'POST',
            dataType: 'json',
            data,
          })
          if (response.status) {
            table.ajax.reload(function(json) {
              // Fix bug: put in setTimeout => added item and move last page
              // but records are still at page = 1, limit = 10
              // Ref: https://datatables.net/forums/discussion/31857/page-draw-is-not-refreshing-the-rows-on-the-table
              setTimeout(function() {
                table.page(json.totalPages - 1).draw('page');
              }, 0);
              toastr.success('Add category successfully')
            });
          } else {
            toastr.error(response.message)
          }
          clearForm(addModal, addForm);
        }
      } catch (error) {
        clearForm(addModal, addForm);
        toastr.error('Something went wrong')
      }
    })
  })
</script>