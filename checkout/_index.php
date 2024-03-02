<?php require_once dirname(__DIR__) . "/inc/components/header.php"; ?>
<?php require_once dirname(__DIR__) . "/inc/utils.php"; ?>
<?php
if (!Auth::isLoggedIn())
    Auth::requireLogin();

if (!isset($conn))
    $conn = require_once dirname(__DIR__) . "/inc/db.php";

// receive product id from cart, after that, get product info depends on productId and cartId
$selectedProductsCart = array();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_cart']))
    $_SESSION['selected_products'] = $_POST['product_cart'];

if (!isset($_SESSION['selected_products'])) redirect(APP_URL);

$selectedProductsCart = [...$_SESSION['selected_products']];

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
                                                            <span class="order-summary__items__list__quantity__title">Qty&nbsp;</span><?php $productCartDetail->quantity; ?></span>
                                                        <span class="order-summary__items__list__price">&nbsp;&nbsp;$<?php echo $productCartDetail->price ?></span>
                                                    </div>
                                                </div>
                                            </li>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                            <button type="button" class="btn-checkout-payment" id="btn-checkout-payment">Go to Payment</button>
                        </div>
                    </div>
                </div>
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
                        <?php foreach ($selectedProductsCart as $productId) :
                            $productCartDetail = Cart::getProductDetailFromCart($conn, $_SESSION['userId'], $productId)['data'];
                            if (isset($productCartDetail)) :
                        ?>
                                <div class="cart-item">
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="border-2 border-bottom border-black border-opacity-25 my-3"></div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-6">
                                            <div class="cart-item-container d-flex align-items-center">
                                                <img class="cart-item__img" src="<?php echo $productCartDetail->imageUrl; ?>" alt="item" />
                                                <p class="cart-item__desc m-0">
                                                    <?php echo $productCartDetail->name; ?>
                                                </p>
                                            </div>
                                        </div>
                                        <div class="col-2 d-flex align-items-center">
                                            <span class="cart-item__price">$<?php echo $productCartDetail->price; ?></span>
                                        </div>
                                        <div class="col-2 d-flex align-items-center">
                                            <input type="number" disabled name="quantity" id="cart-quantity" min="1" value="<?php echo $productCartDetail->quantity; ?>" class="cart-item__input-quantity" />
                                        </div>
                                        <div class="col-2 d-flex align-items-center">
                                            <span class="cart-item__price">$<?php echo $productCartDetail->quantity * $productCartDetail->price; ?></span>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
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
                                <p class="checkout-summary__title m-0">Subtotal</p>
                                <span class="checkout-summary__value">$13,047.00</span>
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
                                <span class="checkout-summary__value">$1.91</span>
                            </div>
                            <div class="checkout-summary__block d-flex justify-content-between my-2">
                                <p class="checkout-summary__title m-0">GST (10%)</p>
                                <span class="checkout-summary__value">$1.91</span>
                            </div>
                            <div class="checkout-summary__block d-flex justify-content-between my-2">
                                <p class="checkout-summary__title m-0">Order Total</p>
                                <span class="checkout-summary__value">$13,068.00</span>
                            </div>
                            <button class="checkout-summary__btn-process my-4">Proceed to Checkout</button>
                            <div class="checkout-summary__zip">
                                <img src="<?php echo APP_URL; ?>/assets/img/zip.svg" alt="zip" class="checkout-summary__zip__img object-fit-contain" />
                                <img src="<?php echo APP_URL; ?>/assets/img/vector.svg" alt="vector" class="checkout-summary__vector object-fit-contain" />
                                <p class="checkout-summary__zip__content m-0">up to 6 months interest free.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="nav-append-container">

            </div>
        </div>
    </div>
</div>

<?php require_once dirname(__DIR__) . "/inc/components/footer.php"; ?>
<script src="<?php echo APP_URL; ?>/js/header/dropdown.js"></script>
<script src="<?php echo APP_URL; ?>/js/header/searchbar.js"></script>
<script>
    $("#checkout-progress").slick({
        slidesToShow: 1,
        slidesToScroll: 1,
        appendDots: $(".nav-append-container"),
        dots: true,
        draggable: false,
        dotsClass: "nav-append",
    });

    const btnPrevControl = document.querySelector(".nav-append  li:nth-child(1) button");
    const btnNextControl = document.querySelector(".nav-append li:nth-child(2) button");
    const btnPrevCheckout = document.getElementById("previous-checkout");
    // const btnCheckoutPayment = document.getElementById("btn-checkout-payment");
    const btnSubmitCheckout = document.querySelector(".btn-submit-checkout");

    btnPrevCheckout.addEventListener("click", (e) => {
        e.preventDefault();
        btnPrevControl.click();
    })

    // btnCheckoutPayment.addEventListener("click", (e) => {
    //     e.preventDefault();
    //     btnNextControl.click();
    // })

    btnNextControl.textContent = "";
    const spanNext = document.createElement("span");
    spanNext.textContent = "Next";
    btnNextControl.appendChild(spanNext);
    btnNextControl.classList.add("d-none")

    btnPrevControl.textContent = "";
    const spanPrev = document.createElement("span");
    spanPrev.textContent = "Previous";
    btnPrevControl.appendChild(spanPrev);
    btnPrevControl.classList.add("d-none")

    const progressCheckout = document.querySelector(".progress-container");
    const progress_first = progressCheckout.querySelector(".progress-bar-container:nth-child(1)");
    const progress_second = progressCheckout.querySelector(".progress-bar-container:nth-child(2)");

    btnNextControl.addEventListener("click", (e) => {
        if (progress_first.classList.contains("loader")) {
            progress_first.classList.remove("loader");
            progress_first.classList.add("completed");
            progress_second.classList.add("active");
            progress_second.classList.add("loader");
        }
    });

    btnPrevControl.addEventListener("click", (e) => {
        if (progress_first.classList.contains("completed")) {
            progress_first.classList.remove("completed");
            progress_first.classList.add("loader");
            progress_second.classList.remove("active");
            progress_second.classList.remove("loader");
        }
    });
</script>
<script>
    // validate form order

    $(document).ready(function() {
        const formOrder = $("#form-order");
        const btnCheckoutPayment = $("#btn-checkout-payment");
        const btnNextCheckout = $(".nav-append li:nth-child(2) button")

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
            foreach ($_SESSION['selected_products'] as $product) : ?>
                productList.push('<?php echo $product ?>');
            <?php endforeach; ?>

            // console.log(productList);
            const data = {
                productList,
                phone: $("#phoneNumber").val(),
                address: $("#address").val()
            }

            const response = await $.ajax({
                method: "POST",
                url: "actions/create-order.php",
                data: data
            })

            const result = JSON.parse(response);

            if (result.status) {
                if (result.data.errorMessage === null) {
                    toastr.success(result.message, "Create Order")
                    setTimeout(() => {
                        btnNextCheckout.click();
                    }, 1000)
                } else {
                    toastr.warning(result.data.errorMessage, "Create Order")
                }
            } else {
                toastr.error("Error when creating order", "Create Order")
            }
        })
    })
</script>