<?php
require_once  dirname(dirname(__DIR__)) . "/inc/init.php";
?>

<?php require_once  dirname(__DIR__) . "/inc/components/header.php" ?>;

<div class="page-wrapper">
  <div class="content">
    <div class="page-header">
      <div class="page-title">
        <h3>Customer List</h3>
        <h4>Manage your customers</h4>
      </div>
    </div>

    <div class="card">
      <div class="card-body">
        <div class="table-top">
          <div class="search-set">
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
                <th>ID</th>
                <th>Avatar</th>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Phone</th>
                <th>Email</th>
                <th>Status</th>
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
        [7, 'asc']
      ],
      ajax: {
        url: '<?php echo GET_CUSTOMERS_API; ?>',
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
            draw: dataObj.data?.draw,
            recordsTotal: dataObj.data?.totalItems,
            recordsFiltered: dataObj.data?.totalItems,
            data: dataObj.data?.items,
            totalPages: dataObj.data?.totalPages
          });
        },
      },
      columnDefs: [{
          name: 'id',
          targets: 0
        },
        {
          targets: 1,
          orderable: false,
          searchable: false,
        },
        {
          name: 'firstName',
          targets: 2
        },
        {
          name: 'lastName',
          targets: 3
        },
        {
          name: 'phoneNumber',
          targets: 4
        },
        {
          name: 'email',
          targets: 5
        },
        {
          name: 'status',
          targets: 6
        },
        {
          name: 'createdAt',
          targets: 7
        },
        {
          targets: 8,
          orderable: false,
          searchable: false,
        },
      ],
      columns: [{
          render: function(data, type, row, meta) {
            return `
              <a class="text-linear-hover" href="<?php echo APP_URL; ?>/admin/customers/details.php?id=${row.id}">
                ${row.id}
              </a>
              `
          }
        },
        {
          render: function(data, type, row, meta) {
            return `
              <div class="name-img-wrapper">
                <a class="product-img details-btn" href="<?php echo APP_URL; ?>/admin/customers/details.php?id=${row.id}" class="product-img">
                  <img src="${row.imageUrl}" />
                </a
              </div>
            `
          }
        },
        {
          render: function(data, type, row, meta) {
            return `
              <a class="text-linear-hover" href="<?php echo APP_URL; ?>/admin/customers/details.php?id=${row.id}">
                ${row.firstName}
              </a>
              `
          }
        },
        {
          render: function(data, type, row, meta) {
            return `
              <a class="text-linear-hover" href="<?php echo APP_URL; ?>/admin/customers/details.php?id=${row.id}">
                ${row.lastName}
              </a>
              `
          }
        },
        {
          data: 'phoneNumber'
        },
        {
          data: 'email'
        },
        {
          render: function(data, type, row, meta) {
            const statusName = row.active ? 'Active' : 'Disabled'
            const badgesColorClass = row.active ? 'bg-lightgreen' : 'bg-lightred'
            return `
              <span class="badges ${badgesColorClass}">${statusName}</span>
            `
          }
        },
        {
          data: 'createdAt'
        },
        {
          render: function(data, type, row, meta) {
            return `
              <a class="me-2 action details-btn" href="<?php echo APP_URL; ?>/admin/customers/details.php?id=${row.id}">
                <img class="action-icon" src="<?php echo APP_URL; ?>/admin/assets/img/icons/eye.svg" alt="img" />
              </a>
              `
          }
        },
      ],
      initComplete: (settings, json) => {
        $('.dataTables_filter').appendTo('#tableSearch')
        $('.dataTables_filter').appendTo('.search-input')
      }
    })
  })
</script>