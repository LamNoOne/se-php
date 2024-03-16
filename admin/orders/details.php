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

$getOrderResult = Order::getOrderByIdV2($conn, $orderId);
if (!$getOrderResult['status']) {
  return redirect(APP_URL . '/admin/500.php');
}

$order = $getOrderResult['data']['order'];
if (!$order) {
  return redirect(APP_URL . '/admin/404.php');
}

?>

<?php require_once  dirname(__DIR__) . "/inc/components/header.php" ?>;

<div class="page-wrapper">
  <div class="content">
    <div class="page-header">
      <div class="page-title">
        <h3>Order Details</h3>
        <h4>Full details of a order</h4>
      </div>
    </div>

    <div class="card">
      <div class="card-body">
        <div class="row">
          <div class="col-lg-8 col-md-6 col-12">
            <h6 class="fw-bold">Customer Information</h6>
            <div class="row gx-3 mt-3">
              <div class="col-lg-3">
                <img style="border-radius: 8px; width: 100%; height: 100%; object-fit: contain;" src="
                  <?php
                  echo $order->customerImageUrl ?
                    $order->customerImageUrl
                    : APP_URL . '/admin/assets/img/no-image.png'
                  ?>">
              </div>
              <div class="col-lg-9 mt-1">
                <div class="row">
                  <div class="col-lg-3 d-flex gap-2 flex-column align-items-start">
                    <p class="fw-bold me-1">Full Name:</p>
                    <p class="fw-bold me-1">Email:</p>
                    <p class="fw-bold me-1">Phone:</p>
                    <p class="fw-bold me-1">Address:</p>
                  </div>
                  <div class="col-lg-9 d-flex gap-2 flex-column align-items-start">
                    <p>
                      <?php echo $order->customerFirstName . ' ' . $order->customerLastName ?>
                    </p>
                    <p><?php echo $order->customerPhoneNumber ?></p>
                    <p><?php echo $order->customerEmail ?></p>
                    <p><?php echo $order->customerAddress ?></p>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="col-lg-4 col-md-6 col-12">
            <h6 class="fw-bold">Order Information</h6>
            <div class="mt-3 mt-1">
              <div class="row">
                <div class="col-lg-6 d-flex gap-2 flex-column align-items-start">
                  <p class="fw-bold me-1">ID:</p>
                  <p class="fw-bold me-1">Shipping Phone:</p>
                  <p class="fw-bold me-1">Shipping Address:</p>
                  <p class="fw-bold me-1">Status:</p>
                </div>
                <div class="col-lg-6 d-flex gap-2 flex-column align-items-end">
                  <p><?php echo $order->id ?></p>
                  <p class="shippingPhone"><?php echo $order->phoneNumber ?></p>
                  <p class="shippingAddress"><?php echo $order->shipAddress ?></p>
                  <p class="status badges
                  <?php
                  $class = 'bg-lightgreen';
                  if ($order->statusId == PENDING) {
                    $class = 'bg-lightred';
                  } else if ($order->statusId == PENDING_CANCEL) {
                    $class = 'bg-lightyellow';
                  } else if ($order->statusId == CANCELLED) {
                    $class = 'bg-lightgrey';
                  } else if ($order->statusId == PAID) {
                    $class = 'bg-lightblue';
                  } else if ($order->statusId == DELIVERING) {
                    $class = 'bg-lightpurple';
                  }
                  echo $class;
                  ?>">
                    <?php echo $order->statusName ?>
                  </p>
                </div>
              </div>
            </div>
            <div class="mt-5 d-flex justify-content-end">
              <a href="javascript:void(0)" class="btn btn-primary" id="openEditModalButton">
                Edit
              </a>
            </div>
          </div>
        </div>
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
                <th>Product Name</th>
                <th>Price</th>
                <th>Quantity</th>
                <th>Total Price</th>
                <th>Created At</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
            </tbody>
          </table>
        </div>
        <div class="table-bottom">
          <div class="d-flex justify-content-end align-items-center gap-3">
            <span class="fw-bold red-text-color">Total Payment:</span>
            <span class="badges bg-lightred"></span>
          </div>
        </div>
      </div>
    </div>
    <div class="d-flex gap-3">
      <a class="btn btn-primary" href="<?php echo APP_URL; ?>/admin/orders">Back</a>
    </div>
  </div>
</div>

<div class="modal fade" id="editModal" aria-hidden="true" aria-labelledby="editModalLabel" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-5" id="editModalLabel">
          Edit Order Information
        </h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"><i class="fas fa-times"></i></button>
      </div>
      <div class="modal-body">
        <form id="editForm">
          <div class="row gx-5">
            <div class="col-lg-12 col-sm-12 col-12">
              <div class="form-group row align-items-center">
                <label class="col col-lg-4">Shipping Phone</label>
                <input class="col col-lg-8" type="text" name="phoneNumber" autofocus />
              </div>
            </div>
            <div class="col-lg-12 col-sm-12 col-12">
              <div class="form-group row align-items-center">
                <label class="col col-lg-4">Shipping Address</label>
                <input class="col col-lg-8" type="text" name="shipAddress" />
              </div>
            </div>
            <div class="col-lg-12 col-sm-12 col-12">
              <div class="form-group row align-items-center">
                <label class="col col-lg-4">Status</label>
                <select name="orderStatusId" class="col col-lg-8 form-select"></select>
              </div>
            </div>
          </div>
        </form>
      </div>
      <div class="modal-footer d-flex justify-content-end">
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
    const totalPaymentBadge = $('.card .table-bottom .badges')

    const clearForm = (form) => {
      form.find('input, textarea, select').val('')
      form.find('select').html('')
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
        [5, 'asc']
      ],
      ajax: {
        url: '<?php echo GET_PRODUCTS_OF_ORDER_API . "?id=$orderId" ?>',
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
          name: 'quantity',
          targets: 3
        },
        {
          name: 'totalPrice',
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
                  <input data-order-id=${row.orderId} data-product-id=${row.id} type="checkbox" />
                  <span class="checkmarks"></span>
                </label>
              `
          }
        },
        {
          render: function(data, type, row, meta) {
            return `
              <div class="name-img-wrapper">
                <a class="product-img details-btn text-linear-hover d-flex align-items-center gap-2" href="<?php echo APP_URL; ?>/admin/products/details.php?id=${row.id}">
                  <img src="${row.imageUrl}" />
                  <span> ${row.name}</span>
                </a
              </div>
            `
          }
        },
        {
          data: 'price'
        },
        {
          render: function(data, type, row, meta) {
            return `
              <div class="edit-order-product">
                <input 
                  type="number"
                  min="1"
                  name="quantity"
                  value="${row.quantity}"
                  style="display: none;"
                />
                <span>${row.quantity}</span>
              </div>
            `
          }
        },
        {
          data: 'totalPrice'
        },
        {
          data: 'createdAt'
        },
        {
          render: function(data, type, row, meta) {
            return `
              <div class="confirm-buttons">
                <button
                  class="btn btn-primary update-order-product-submit-button"
                  data-product-id="${row.id}"
                  data-order-id="${row.orderId}"
                >
                  Update
                </button>
                <button class="btn btn-cancel update-order-product-cancel-button">Cancel</button>
              </div>
              <div class="actions">
                <a class="me-2 action details-btn" href="<?php echo APP_URL; ?>/admin/products/details.php?id=${row.id}">
                  <img class="action-icon" src="<?php echo APP_URL; ?>/admin/assets/img/icons/eye.svg" alt="img" />
                </a>
                <a
                  class="me-2 edit-button action"
                  href="javascript:void(0)"
                >
                  <img class="action-icon" src="<?php echo APP_URL; ?>/admin/assets/img/icons/edit.svg" alt="img" />
                </a>
                <a
                  class="action delete-btn"
                  data-product-id="${row.id}"
                  data-order-id="${row.orderId}"
                  href="javascript:void(0)"
                >
                  <img class="action-icon" src="<?php echo APP_URL; ?>/admin/assets/img/icons/delete.svg" alt="img" />
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

    // handle edit quantity of product
    $('#table').on('draw.dt', function(event, settings) {
      const {
        totalPayment
      } = settings.json || {}
      // change total payment on UI
      totalPaymentBadge.text(totalPayment);

      $('#table tbody tr').each(async function() {
        const tr = $(this)
        const editButton = tr.find('.actions .edit-button')
        const confirmButtons = tr.find('.confirm-buttons')
        const actions = tr.find('.actions')
        const quantityInput = tr.find('.edit-order-product input[name]')
        const valueSpan = tr.find('.edit-order-product span')
        const submitButton = tr.find('.update-order-product-submit-button')
        const cancelButton = tr.find('.update-order-product-cancel-button')

        const toggleUpdateOrderProduct = () => {
          if (actions.css('display') === 'none') {
            actions.show()
            valueSpan.show()
            confirmButtons.hide()
            quantityInput.hide()
            return;
          }
          actions.hide()
          valueSpan.hide()
          confirmButtons.show()
          quantityInput.show()
        }
        editButton.click(function() {
          toggleUpdateOrderProduct();
        })

        submitButton.click(async function() {
          try {
            const orderId = $(this).data('orderId')
            const productId = $(this).data('productId')
            const quantityInputValue = quantityInput.val();
            if (quantityInputValue === '') {
              toastr.error('Quantity is required');
              return;
            }
            const quantity = parseInt(quantityInput.val())
            if (quantity < 1) {
              toastr.error('Quantity must be greater than 0');
              return;
            }

            const response = await $.ajax({
              url: `<?php echo UPDATE_ORDER_PRODUCT_API; ?>`,
              type: 'POST',
              dataType: 'json',
              data: {
                productId,
                orderId,
                quantity
              }
            })

            if (response.status) {
              const currentPage = table.page.info().page;
              toggleUpdateOrderProduct();
              table.page(currentPage).draw('page')
              totalPaymentBadge.text(response.data.totalPayment);
              toastr.success('Update quantity successfully')
              return;
            }
            toggleUpdateOrderProduct();
            toastr.error(response.message)
          } catch (error) {
            toggleUpdateOrderProduct();
            toastr.error('Something went wrong')
          }
        })

        cancelButton.click(function() {
          toggleUpdateOrderProduct();
        })
      })
    })

    // handle delete order product
    $('#table tbody').on('click', '.delete-btn', function() {
      const orderId = $(this).data('orderId')
      const productId = $(this).data('productId')
      Swal
        .fire({
          title: 'Delete Order Product?',
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
                url: '<?php echo DELETE_ORDER_PRODUCT_API ?>',
                type: 'POST',
                dataType: 'json',
                data: {
                  orderId,
                  productId
                },
              })

              if (response.status) {
                goToCurrentPage(table)
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

    // handle delete order products by select
    $('#deleteBySelectBtn').click(function() {
      const selectAll = tableEle.find('#select-all')
      const checkedBoxes = tableEle.find(
        'input[type="checkbox"]:checked:not([id="select-all"])'
      )
      let checkedIds = [];
      checkedBoxes.each(function() {
        checkedIds = [
          ...checkedIds,
          {
            orderId: $(this).data('orderId'),
            productId: $(this).data('productId')
          }
        ]
      })

      Swal
        .fire({
          title: 'Delete Selected Order Products?',
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
                url: '<?php echo DELETE_ORDER_PRODUCTS_API; ?>',
                type: 'POST',
                dataType: 'json',
                data: {
                  ids: checkedIds
                },
              })

              if (response.status) {
                goToCurrentPage(table, true)
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

    // handle edit order info
    const editFormId = '#editForm'
    const editModalId = '#editModal'
    const editForm = $(editFormId)
    const editModal = $(editModalId)
    const editModalBootstrapInstance = bootstrap.Modal.getOrCreateInstance(document.getElementById('editModal'))
    const phoneNumberInput = $('#editForm input[name="phoneNumber"]')
    const shipAddressInput = $('#editForm input[name="shipAddress"]')
    const statusSelect = $('#editForm select[name="orderStatusId"]')
    const editFormSubmitButton = $(editModalId + ' .modal-footer button[type="submit"]')
    editModal.on("hidden.bs.modal", function() {
      clearForm(editForm);
    });
    const searchParams = new URLSearchParams(window.location.search)
    let orderStatuses = []
    $('#openEditModalButton').click(async function() {
      try {
        const getOrderStatuses = $.ajax({
          url: `<?php echo GET_ORDER_STATUSES_API; ?>?limit=-1`,
          type: 'GET',
          dataType: 'json'
        })
        const getOrder = $.ajax({
          url: `<?php echo GET_ORDER_BY_ID_API; ?>?id=${searchParams.get('id')}`,
          type: 'GET',
          dataType: 'json'
        })

        const [orderStatusesResponse, orderResponse] = await Promise.all([
          getOrderStatuses,
          getOrder
        ])

        if (orderStatusesResponse.status && orderResponse.status) {
          orderStatuses = orderStatusesResponse.data?.items || []
          const order = orderResponse.data?.order || {}
          phoneNumberInput.val(order.phoneNumber)
          shipAddressInput.val(order.shipAddress)
          statusSelect.html(orderStatuses.map((status) => `
            <option
              ${order.statusId == status.id ? 'selected' : ''}
              value="${status.id}"
            >
              ${status.name}
            </option>
          `).join(''))
          editModalBootstrapInstance.show()
          return
        }
        toastr.error('Something went wrong')
      } catch (error) {
        toastr.error('Something went wrong')
      }
    })
    editFormSubmitButton.click(function() {
      editForm.submit()
    })
    editForm.validate({
      rules: {
        phoneNumber: {
          required: true
        },
        shipAddress: {
          required: true
        },
        orderStatusId: {
          required: true
        }
      },
    })
    editForm.submit(async function(event) {
      try {
        event.preventDefault()
        if ($(this).valid()) {
          let data = $(this).serializeArray().reduce((acc, item) => {
            return {
              ...acc,
              [item.name]: item.value
            }
          }, {})

          data = {
            ...data,
            id: searchParams.get('id')
          }

          const response = await $.ajax({
            url: '<?php echo UPDATE_ORDER_API; ?>',
            type: 'POST',
            dataType: 'json',
            data,
          })
          if (response.status) {
            const pendingStatusId = <?php echo PENDING; ?>;
            const pendingCancelStatusId = <?php echo PENDING_CANCEL; ?>;
            const cancelledStatusId = <?php echo CANCELLED; ?>;
            const paidStatusId = <?php echo PAID; ?>;
            const deliveringStatusId = <?php echo DELIVERING; ?>;
            const deliveredStatusId = <?php echo DELIVERED; ?>;
            let badgesColorClass = 'bg-lightgreen'

            $('.card .shippingPhone').text(data.phoneNumber)
            $('.card .shippingAddress').text(data.shipAddress)
            const currentStatus = orderStatuses.find(
              status => status.id === data.orderStatusId
            )
            if (currentStatus.id == pendingStatusId) {
              badgesColorClass = 'bg-lightred'
            } else if (currentStatus.id == pendingCancelStatusId) {
              badgesColorClass = 'bg-lightyellow'
            } else if (currentStatus.id == cancelledStatusId) {
              badgesColorClass = 'bg-lightgrey'
            } else if (currentStatus.id == paidStatusId) {
              badgesColorClass = 'bg-lightblue'
            } else if (currentStatus.id == deliveringStatusId) {
              badgesColorClass = 'bg-lightpurple'
            }
            $('.card .status')
              .text(currentStatus.name)
              .removeClass('bg-lightred')
              .removeClass('bg-lightyellow')
              .removeClass('bg-lightgrey')
              .removeClass('bg-lightred')
              .removeClass('bg-lightblue')
              .removeClass('bg-lightpurple')
              .removeClass('bg-lightgreen')
              .addClass(badgesColorClass)
            toastr.success('Edit order successfully')
          } else {
            toastr.error(response.message)
          }
          editModalBootstrapInstance.hide()
        }
      } catch (error) {
        editModalBootstrapInstance.hide()
        toastr.error('Something went wrong')
      }
    })
  })
</script>