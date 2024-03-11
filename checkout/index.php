<?php require_once dirname(__DIR__) . "/inc/components/header.php"; ?>
<?php require_once dirname(__DIR__) . "/inc/utils.php"; ?>
<?php
Auth::requireLogin();

if (!isset($conn))
    $conn = require_once dirname(__DIR__) . "/inc/db.php";

// receive product id from cart, after that, get product info depends on productId and cartId
$selectedProductsCart = array();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_cart']))
    $_SESSION['selected_products'] = $_POST['product_cart'];

if (isset($_SESSION['selected_products']))
    $selectedProductsCart = [...$_SESSION['selected_products']];

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['product_id']) && isset($_GET['quantity'])) {
    $productId = $_GET['product_id'];
    $productQuantity = $_GET['quantity'];
    $productSelectedCheckout = Product::getProductById($conn, $productId);
}

// print_r($selectedProductsCart);

?>
<div id="main-content" class="main-content">
    <div id="checkout-container">
        <div class="container">
            <div class="row">
                <div class="col-12 d-flex justify-content-between align-items-start">
                    <h1 class="checkout__header m-0">Checkout</h1>
                    <div class="progress-container d-flex align-items-start">
                        <div class="progress-bar-container d-flex flex-column align-items-center active loader">
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
                        <div class="progress-bar-container d-flex flex-column align-items-center">
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
                <div class="row d-flex flex-nowrap">
                    <div class="col-8 ms-1">
                        <h5 class="checkout__title mb-4">Shipping Address</h5>
                        <div class="checkout__border border-bottom border-black border-opacity-25"></div>
                        <form action="" method="POST" class="checkout-procedure" id="form-order">
                            <fieldset>
                                <div class="checkout-procedure__form d-flex flex-column gap-1 my-3">
                                    <label for="email" class="checkout-procedure__form__label">
                                        Email
                                        <span>&nbsp;*</span>
                                    </label>
                                    <input type="email" placeholder="Your Email" name="email" id="email" value="<?php echo $_SESSION['email']; ?>" class="checkout-procedure__form__input" disabled />
                                </div>

                                <div class="checkout-procedure__form d-flex flex-column gap-1 my-3">
                                    <label for="firstname" class="checkout-procedure__form__label">
                                        First Name
                                        <span>&nbsp;*</span>
                                    </label>
                                    <input type="text" placeholder="Your First Name" name="firstname" id="firstname" value="<?php echo $_SESSION['firstName']; ?>" class="checkout-procedure__form__input" disabled />
                                </div>

                                <div class="checkout-procedure__form d-flex flex-column gap-1 my-3">
                                    <label for="lastname" class="checkout-procedure__form__label">
                                        Last Name
                                        <span>&nbsp;*</span>
                                    </label>
                                    <input type="text" placeholder="Your Last Name" name="lastname" id="lastname" value="<?php echo $_SESSION['lastName'] ?>" class="checkout-procedure__form__input" disabled />
                                </div>

                                <div class="checkout-procedure__form d-flex flex-column gap-1 my-3">
                                    <label for="phoneNumber" class="checkout-procedure__form__label">
                                        Phone Number
                                        <span>&nbsp;*</span>
                                    </label>
                                    <input type="text" placeholder="Your Phone Number" name="phoneNumber" id="phoneNumber" <?php if (isset($_SESSION['phoneNumber'])) : ?> value="<?php echo $_SESSION['phoneNumber']; ?>" <?php endif; ?> class="checkout-procedure__form__input" />
                                </div>

                                <div class="checkout-procedure__form d-flex flex-column gap-1 my-3">
                                    <label for="address" class="checkout-procedure__form__label">
                                        Street Address
                                        <span>&nbsp;*</span>
                                    </label>
                                    <input type="text" placeholder="Your Address" name="address" id="address" <?php if (isset($_SESSION['address'])) : ?> value="<?php echo $_SESSION['address']; ?>" <?php endif; ?> class="checkout-procedure__form__input" />
                                </div>
                                <button class="d-none btn-submit-checkout" id="btn-submit-checkout" type="button">Submit</button>
                            </fieldset>
                        </form>
                    </div>
                    <div class="col-4">
                        <div class="order-summary">
                            <h3 class="order-summary__title">Order Summary</h3>
                            <div class="order-summary__border border-bottom border-black border-opacity-25"></div>
                            <div class="order-summary__items">
                                <div class="order-summary__items__info d-flex py-3">
                                    <span class="order-summary__items__quantity" 3</span>
                                        <p class="order-summary__items__desc m-0">&nbsp;Items in Cart</p>
                                </div>
                                <ul class="order-summary__items__list list-unstyled m-0 d-flex flex-column gap-3">
                                    <?php if (!isset($productSelectedCheckout)) : ?>
                                        <?php foreach ($selectedProductsCart as $productId) :
                                            $productCartDetail = Cart::getProductDetailFromCart($conn, $_SESSION['userId'], $productId)['data'];
                                            if (isset($productCartDetail)) :
                                        ?>
                                                <li class="order-summary__items__list__product d-flex justify-content-start align-items-start gap-3">
                                                    <img src="<?php echo $productCartDetail->imageUrl; ?>" alt="product" class="object-fit-contain" />
                                                    <div class="order-summary__items__list__info d-flex flex-column">
                                                        <p class="order-summary__items__list__desc m-0">
                                                            <?php echo $productCartDetail->name; ?>
                                                        </p>
                                                        <div class="order-summary__items__list__specs d-flex">
                                                            <span class="order-summary__items__list__quantity">
                                                                <span class="order-summary__items__list__quantity__title">Qty&nbsp;</span><?php echo $productCartDetail->quantity; ?></span>
                                                            <span class="order-summary__items__list__price">&nbsp;&nbsp;$<?php echo $productCartDetail->price ?></span>
                                                        </div>
                                                    </div>
                                                </li>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    <?php else : ?>
                                        <li class="order-summary__items__list__product d-flex justify-content-start align-items-start gap-3">
                                            <img src="<?php echo $productSelectedCheckout->imageUrl; ?>" alt="product" class="object-fit-contain" />
                                            <div class="order-summary__items__list__info d-flex flex-column">
                                                <p class="order-summary__items__list__desc m-0">
                                                    <?php echo $productSelectedCheckout->name; ?>
                                                </p>
                                                <div class="order-summary__items__list__specs d-flex">
                                                    <span class="order-summary__items__list__quantity">
                                                        <span class="order-summary__items__list__quantity__title">Qty&nbsp;</span><?php echo $productQuantity; ?></span>
                                                    <span class="order-summary__items__list__price">&nbsp;&nbsp;$<?php echo $productSelectedCheckout->price ?></span>
                                                </div>
                                            </div>
                                        </li>
                                    <?php endif; ?>
                                </ul>
                            </div>
                            <?php if (!isset($productSelectedCheckout)) : ?>
                                <button type="button" class="btn-checkout-payment" id="btn-checkout-payment">Go to Payment</button>
                            <?php else : ?>
                                <button type="button" class="btn-checkout-payment" id="btn-checkout-payment-single-product">Go to Payment</button>
                            <?php endif; ?>
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
    // validate form order
    $(document).ready(function() {
        const formOrder = $("#form-order");
        const btnCheckoutPayment = $("#btn-checkout-payment");
        const btnCheckoutPaymentSingleProduct = $("#btn-checkout-payment-single-product");

        jQuery.validator.addMethod("valid_phone", function(value) {
            const regex = /(84|0[3|5|7|8|9])+([0-9]{8})\b/g;
            return value.trim().match(regex);
        });

        formOrder.validate({
            rules: {
                phoneNumber: {
                    required: true,
                    valid_phone: true
                },
                address: {
                    required: true,
                    minlength: 2
                },
            },
            messages: {
                phoneNumber: {
                    required: "Please enter your phone number",
                    valid_phone: "Please enter a valid phone number"
                },
                address: {
                    required: "Please enter your address"
                },
            }
        })

        btnCheckoutPayment.click(async function(e) {
            e.preventDefault();
            if (!formOrder.valid()) {
                return false;
            }

            // get product list
            const productList = new Array();
            <?php
            if (empty($productSelectedCheckout)) :
                foreach ($_SESSION['selected_products'] as $product) : ?>
                    productList.push('<?php echo $product ?>');
            <?php endforeach;
            endif;
            ?>

            console.log(productList);

            // console.log(productList);
            const data = {
                productList,
                phone: $("#phoneNumber").val(),
                address: $("#address").val()
            }

            const response = await $.ajax({
                method: "POST",
                url: "actions/create-order-cart.php",
                data: data
            })

            const result = JSON.parse(response);
            console.log(result);

            if (result.status) {
                if (result.data.errorMessage === null) {
                    toastr.success(result.message, "Create Order")
                    const orderId = result.data.orderId
                    setTimeout(() => {
                        window.location.replace(`<?php echo APP_URL; ?>/payment?orderId=${orderId}`);
                    }, 500)
                } else {
                    toastr.warning(result.data.errorMessage, "Create Order")
                }
            } else {
                toastr.error("Error when creating order", "Create Order")
            }
        })


        btnCheckoutPaymentSingleProduct.click(async function(e) {
            e.preventDefault();
            if (!formOrder.valid()) {
                return false;
            }

            // get product list
            const productCheckout = {
                productId: "<?php echo isset($productId) ? $productId : 0 ?>",
                productQuantity: "<?php echo isset($productQuantity) ? $productQuantity : 0 ?>"
            }

            // console.log(productList);
            const data = {
                productCheckout,
                phone: $("#phoneNumber").val(),
                address: $("#address").val()
            }

            console.log(data)

            const response = await $.ajax({
                method: "POST",
                url: "actions/create-order-product.php",
                data: data
            })

            const result = JSON.parse(response);
            console.log(result);

            if (result.status) {
                if (result.data.errorMessage === null) {
                    toastr.success(result.message, "Create Order")
                    const orderId = result.data.orderId
                    setTimeout(() => {
                        window.location.replace(`<?php echo APP_URL; ?>/payment?orderId=${orderId}`);
                    }, 500)
                } else {
                    toastr.warning(result.data.errorMessage, "Create Order")
                }
            } else {
                toastr.error("Error when creating order", "Create Order")
            }
        })
    })
</script>