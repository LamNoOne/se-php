<?php require_once dirname(__DIR__) . "/inc/components/header.php"; ?>
<?php require_once dirname(__DIR__) . "/inc/utils.php"; ?>
<?php
Auth::requireLogin();

if (!isset($_GET['orderId'])) redirect(APP_URL);

if (!isset($conn))
    $conn = require_once dirname(__DIR__) . "/inc/db.php";

$paymentId = $_GET['orderId'];
$orderData = Order::getOrderById($conn, $paymentId)['data'];
$orderDetail = $orderData['orderDetail'];
$order = $orderData['order'];

if (intval($order->orderStatusId) !== 1) redirect(APP_URL);

$totalPrice = array_reduce($orderDetail, fn ($total, $orderItem) => $total += $orderItem->quantity * $orderItem->price, 0);
?>
<div id="main-content" class="main-content">
    <div id="checkout-container">
        <div class="container">
            <div class="row">
                <div class="col-12 d-flex justify-content-between align-items-start">
                    <h1 class="checkout__header m-0">Checkout</h1>
                    <div class="progress-container d-flex align-items-start">
                        <div class="progress-bar-container d-flex flex-column align-items-center active completed">
                            <div class="d-flex align-items-center">
                                <div class="progress-bar-container__line"></div>
                                <div class="progress-bar-container__period progress-bar-container__number">1</div>
                                <div class="progress-bar-container__period progress-bar-container__check">
                                    <i class="fa-solid fa-check"></i>
                                </div>
                                <div class="progress-bar-container__period progress-bar-container__loader">
                                    <i class="fa-solid fa-spinner"></i>
                                </div>
                                <div class="progress-bar-container__line"></div>
                            </div>
                            <p class="progress-bar-container__desc m-0">Shipping</p>
                        </div>
                        <div class="progress-bar-container d-flex flex-column align-items-center active loader">
                            <div class="d-flex align-items-center">
                                <div class="progress-bar-container__line"></div>
                                <div class="progress-bar-container__period">2</div>
                                <div class="progress-bar-container__period progress-bar-container__check">
                                    <i class="fa-solid fa-check"></i>
                                </div>
                                <div class="progress-bar-container__period progress-bar-container__loader">
                                    <i class="fa-solid fa-spinner"></i>
                                </div>
                                <div class="progress-bar-container__line"></div>
                            </div>
                            <p class="progress-bar-container__desc m-0">Review & Payments</p>
                        </div>
                    </div>
                </div>
            </div>
            <div id="checkout-progress">
                <div class="row d-flex">
                    <div class="col-8">
                        <div class="row">
                            <div class="col-6">
                                <span class="cart-header">Item</span>
                            </div>
                            <div class="col-2">
                                <span class="cart-header">Price</span>
                            </div>
                            <div class="col-2">
                                <span class="cart-header">Quantity</span>
                            </div>
                            <div class="col-2">
                                <span class="cart-header">Subtotal</span>
                            </div>
                        </div>
                        <?php foreach ($orderDetail as $orderProduct) : ?>
                            <div class="cart-item">
                                <div class="row">
                                    <div class="col-12">
                                        <div class="border-2 border-bottom border-black border-opacity-25 my-3"></div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-6">
                                        <div class="cart-item-container d-flex align-items-center">
                                            <img class="cart-item__img" src="<?php echo $orderProduct->imageUrl; ?>" alt="item" />
                                            <p class="cart-item__desc m-0">
                                                <?php echo $orderProduct->name; ?>
                                            </p>
                                        </div>
                                    </div>
                                    <div class="col-2 d-flex align-items-center">
                                        <span class="cart-item__price">$<?php echo $orderProduct->price; ?></span>
                                    </div>
                                    <div class="col-2 d-flex align-items-center">
                                        <input type="number" disabled name="quantity" id="cart-quantity" min="1" value="<?php echo $orderProduct->quantity; ?>" class="cart-item__input-quantity" />
                                    </div>
                                    <div class="col-2 d-flex align-items-center">
                                        <span class="cart-item__price">$<?php echo $orderProduct->quantity * $orderProduct->price; ?></span>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                        <div class="cart-item-control">
                            <div class="row">
                                <div class="col-12">
                                    <div class="border-2 border-bottom border-black border-opacity-25 my-3"></div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12 d-flex justify-content-between">
                                    <button type="button" class="previous-checkout" id="previous-checkout">Back</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="checkout-summary">
                            <h3 class="checkout-summary__header">Summary</h3>
                            <div class="checkout-summary__block d-flex justify-content-between my-2">
                                <p class="checkout-summary__title m-0">Customer:</p>
                                <span class="checkout-summary__value"><?php echo $order->firstName . " " . $order->lastName; ?></span>
                            </div>
                            <div class="checkout-summary__block d-flex justify-content-between my-2">
                                <p class="checkout-summary__title m-0">Address:</p>
                                <span class="checkout-summary__value"><?php echo $order->shipAddress; ?></span>
                            </div>
                            <div class="checkout-summary__block d-flex justify-content-between my-2">
                                <p class="checkout-summary__title m-0">Phone:</p>
                                <span class="checkout-summary__value"><?php echo $order->phoneNumber; ?></span>
                            </div>
                            <div class="checkout-summary__block d-flex justify-content-between my-2">
                                <p class="checkout-summary__title m-0">Shipping</p>
                                <span class="checkout-summary__value">Free</span>
                            </div>
                            <div class="checkout-summary__block d-flex justify-content-between my-2">
                                <p class="checkout-summary__note m-0">
                                    (Standard Rate - Price may vary depending on the item/destination. TECS Staff will
                                    contact you.)
                                </p>
                            </div>
                            <div class="checkout-summary__block d-flex justify-content-between my-2">
                                <p class="checkout-summary__title m-0">Tax</p>
                                <span class="checkout-summary__value">Free</span>
                            </div>
                            <div class="checkout-summary__block d-flex justify-content-between my-2">
                                <p class="checkout-summary__title m-0">GST</p>
                                <span class="checkout-summary__value">Free</span>
                            </div>
                            <div class="checkout-summary__block d-flex justify-content-between my-2">
                                <p class="checkout-summary__title m-0">Order Total</p>
                                <span class="checkout-summary__value">$<?php echo $totalPrice; ?></span>
                            </div>
                            <!-- Payment with Paypal -->
                            <div class="payment-container pt-2">
                                <div class="overlay hidden">
                                    <div class="overlay-content">
                                        <img src="<?php echo APP_URL; ?>/assets/img/loading.gif" alt="Processing..." />
                                    </div>
                                </div>
                                <div class="payment-body">
                                    <!-- Display response if error happen -->
                                    <div id="paymentResponse" class="hidden"></div>
                                    <!-- Display payment button -->
                                    <div id="paypal-button-container"></div>
                                </div>
                            </div>
                            <!-- End payment with Paypal -->
                            <div class="checkout-summary__zip">
                                <img src="<?php echo APP_URL; ?>/assets/img/zip.svg" alt="zip" class="checkout-summary__zip__img object-fit-contain" />
                                <img src="<?php echo APP_URL; ?>/assets/img/vector.svg" alt="vector" class="checkout-summary__vector object-fit-contain" />
                                <p class="checkout-summary__zip__content m-0">up to 6 months interest free.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once dirname(__DIR__) . "/inc/components/footer.php"; ?>
<script src="<?php echo APP_URL; ?>/js/header/dropdown.js"></script>
<script src="<?php echo APP_URL; ?>/js/header/searchbar.js"></script>
<script>
    paypal.Buttons({
        // Sets up the transaction when a payment button is clicked
        createOrder: (data, actions) => {
            console.log('data create order: ', data)
            console.log('actions create order: ', actions)
            return actions.order.create({
                "intent": "CAPTURE",
                "purchase_units": [{
                    "custom_id": "<?php echo $paymentId ?>",
                    "amount": {
                        "currency_code": "<?php echo CURRENCY ?>",
                        "value": "<?php echo $totalPrice ?>"
                    }
                }]
            });
        },
        // Finalize the transaction after payer approval
        onApprove: (data, actions) => {
            console.log('data approve: ', data)
            console.log('actions approve: ', actions)
            return actions.order.capture().then(function(orderData) {
                console.log("Order Data: ", orderData)
                setProcessing(true);

                var postData = {
                    paypal_order_check: 1,
                    order_id: orderData.id
                };
                fetch('actions/paypal-validate.php', {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json'
                        },
                        body: encodeFormData(postData)
                    })
                    .then((response) => response.json())
                    .then((result) => {
                        if (result.status == 1) {
                            console.log(result)
                            // window.location.href = "transaction.php?checkout_ref_id=" + result.ref_id;
                        } else {
                            const messageContainer = document.querySelector("#paymentResponse");
                            messageContainer.classList.remove("hidden");
                            messageContainer.textContent = result.msg;

                            setTimeout(function() {
                                messageContainer.classList.add("hidden");
                                messageContainer.textContent = "";
                            }, 5000);
                        }
                        setProcessing(false);
                    })
                    .catch(error => console.log(error));
            });
        }
    }).render('#paypal-button-container');

    const encodeFormData = (data) => {
        var form_data = new FormData();

        for (var key in data) {
            form_data.append(key, data[key]);
        }
        return form_data;
    }

    // Show a loader on payment form processing
    const setProcessing = (isProcessing) => {
        if (isProcessing) {
            document.querySelector(".overlay").classList.remove("hidden");
        } else {
            document.querySelector(".overlay").classList.add("hidden");
        }
    }
</script>