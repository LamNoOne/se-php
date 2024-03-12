<?php require_once  dirname(__DIR__) . "/inc/init.php"; ?>

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

<?php require_once "./inc/components/footer.php" ?>;

<script>
  $(document).ready(function() {
    $('table tbody').on('click', '#delete-btn', function() {
      const id = $(this).data('id')
      Swal
        .fire(sweetalertDeleteConfirmConfig(
          'Delete Product?',
          'This action cannot be reverted. Are you sure?'
        ))
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

    const DEFAULT_PAGE = 1
    const DEFAULT_LIMIT = 10
    const DEFAULT_SEARCH = ''
    const DEFAULT_SORT_BY = 'createdAt'
    const DEFAULT_ORDER = 'asc'

    const table = $('#table').DataTable({
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
        info: '_START_ - _END_ of _TOTAL_ items',
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
            data: dataObj.items
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
              <a class="me-3" href="edit-product.php?id=${row.id}">
                <img src="assets/img/icons/edit.svg" alt="img" />
              </a>
              <a data-id="${row.id}" id="delete-btn" href="javascript:void(0);">
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

    // $('#table').on('init.dt', function() {
    //   const isLastPage = Boolean(sessionStorage.getItem('isLastPage'))
    //   sessionStorage.removeItem('isLastPage')
    //   if (isLastPage) {
    //     table.page('last').draw('page')
    //   }
    // })
  })
</script>