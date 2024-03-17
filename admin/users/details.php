<?php
require_once  dirname(dirname(__DIR__)) . "/inc/init.php";
require_once  dirname(dirname(__DIR__)) . "/inc/utils.php";
$conn = require_once  dirname(dirname(__DIR__)) . "/inc/db.php";

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
  return redirectByServer(APP_URL . '/admin/orders/');
}
if (!isset($_GET['id'])) {
  return redirectByServer(APP_URL . '/admin/orders/');
}

$orderId = $_GET['id'];

$customer = User::getUserById($conn, $orderId);

if (!$customer) {
  return redirect(APP_URL . '/admin/404.php');
}

?>

<?php require_once  dirname(__DIR__) . "/inc/components/header.php" ?>;

<div class="page-wrapper">
  <div class="content">
    <div class="page-header">
      <div class="page-title">
        <h3>Customer Details</h3>
        <h4>Full details of a customer </h4>
      </div>
    </div>

    <div class="card">
      <div class="card-body">
        <div class="row">
          <div class="col-lg-12 col-md-12 col-12">
            <h6 class="fw-bold">Customer Information</h6>
            <div class="row gx-3 mt-3">
              <div class="col-lg-2">
                <img style="border-radius: 8px; width: 100%; height: 100%; object-fit: contain;" src="
                  <?php
                  echo $customer->imageUrl ?
                    $customer->imageUrl
                    : APP_URL . '/admin/assets/img/no-image.png'
                  ?>">
              </div>
              <div class="col-lg-10 mt-1">
                <div class="row">
                  <div class="col-lg-2 d-flex gap-2 flex-column align-items-start">
                    <p class="fw-bold me-1">Full Name:</p>
                    <p class="fw-bold me-1">Email:</p>
                    <p class="fw-bold me-1">Phone:</p>
                    <p class="fw-bold me-1">Address:</p>
                  </div>
                  <div class="col-lg-10 d-flex gap-2 flex-column align-items-start">
                    <p>
                      <?php echo $customer->firstName . ' ' . $customer->lastName ?>
                    </p>
                    <p><?php echo $customer->phoneNumber ?></p>
                    <p><?php echo $customer->email ?></p>
                    <p><?php echo $customer->address ?></p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
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
                <th>Total Payment</th>
                <th>Status</th>
                <th>Created At</th>
                <th>Updated At</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
            </tbody>
          </table>
        </div>
      </div>
    </div>
    <div class="d-flex gap-3">
      <a class="btn btn-primary" href="<?php echo APP_URL; ?>/admin/orders">Back</a>
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
    const totalPaymentBadge = $('.card .table-bottom .badges')

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
        [3, 'desc']
      ],
      ajax: {
        url: '<?php echo GET_ORDERS_BY_USER_ID_API . "?userId=$customer->id" ?>',
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
            totalPages: dataObj.data?.totalPages,
            totalPayment: dataObj.data?.totalPayment
          });
        },
      },
      columnDefs: [{
          name: 'id',
          targets: 0
        },
        {
          name: 'totalPrice',
          targets: 1
        },
        {
          name: 'status',
          targets: 2
        },
        {
          name: 'createdAt',
          targets: 3
        },
        {
          name: 'updatedAt',
          targets: 4
        },
        {
          targets: 5,
          orderable: false,
          searchable: false,
        },
      ],
      columns: [{
          render: function(data, type, row, meta) {
            return `
              <a
                class="text-linear-hover"
                href="<?php echo APP_URL ?>/admin/orders/details.php?id=${row.id}"
              >
                ${row.id}
              </a>
            `
          }
        },
        {
          data: 'totalPrice'
        },
        {
          render: function(data, type, row, meta) {
            const pendingStatusId = <?php echo PENDING; ?>;
            const pendingCancelStatusId = <?php echo PENDING_CANCEL; ?>;
            const cancelledStatusId = <?php echo CANCELLED; ?>;
            const paidStatusId = <?php echo PAID; ?>;
            const deliveringStatusId = <?php echo DELIVERING; ?>;
            const deliveredStatusId = <?php echo DELIVERED; ?>;
            let badgesColorClass = 'bg-lightgreen'
            if (row.statusId == pendingStatusId) {
              badgesColorClass = 'bg-lightred'
            } else if (row.statusId == pendingCancelStatusId) {
              badgesColorClass = 'bg-lightyellow'
            } else if (row.statusId == cancelledStatusId) {
              badgesColorClass = 'bg-lightgrey'
            } else if (row.statusId == paidStatusId) {
              badgesColorClass = 'bg-lightblue'
            } else if (row.statusId == deliveringStatusId) {
              badgesColorClass = 'bg-lightpurple'
            }

            return `
              <span class="badges ${badgesColorClass}">${row.statusName}</span>
            `
          }
        },
        {
          data: 'createdAt'
        },
        {
          data: 'updatedAt'
        },
        {
          render: function(data, type, row, meta) {
            return `
              <div class="actions">
                <a class="me-2 action details-btn" href="<?php echo APP_URL; ?>/admin/orders/details.php?id=${row.id}">
                  <img class="action-icon" src="<?php echo APP_URL; ?>/admin/assets/img/icons/eye.svg" alt="img" />
                </a>
              </div>
              `
          }
        },
      ],
      initComplete: (settings, json) => {
        $('.dataTables_filter').appendTo('#tableSearch')
        $('.dataTables_filter').appendTo('.search-input')

        // In order to switch to old page of deleted item
        if (sessionStorage.getItem('pageInfo')) {
          const pageInfo = JSON.parse(sessionStorage.getItem('pageInfo'));
          sessionStorage.removeItem('pageInfo');
          goToCurrentPage(table, true, pageInfo);
        }
      },
    })
  })
</script>