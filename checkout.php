<?php require_once "inc/components/header.php"; ?>

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
                        <form action="" method="post" class="checkout-procedure">
                            <fieldset>
                                <div class="checkout-procedure__form d-flex flex-column gap-1 my-3">
                                    <label for="email" class="checkout-procedure__form__label">Email<span>&nbsp;*</span></label>
                                    <input type="email" placeholder="Your Email" name="email" id="email" value="daclamtrannguyen@gmail.com" class="checkout-procedure__form__input" disabled />
                                </div>
                                <div class="checkout-procedure__form d-flex flex-column gap-1 my-3">
                                    <label for="firstname" class="checkout-procedure__form__label">First Name<span>&nbsp;*</span></label>
                                    <input type="text" placeholder="Your First Name" name="firstname" id="firstname" value="Dac" class="checkout-procedure__form__input" disabled />
                                </div>
                                <div class="checkout-procedure__form d-flex flex-column gap-1 my-3">
                                    <label for="lastname" class="checkout-procedure__form__label">Last Name<span>&nbsp;*</span></label>
                                    <input type="text" placeholder="Your Last Name" name="lastname" id="lastname" value="Lam" class="checkout-procedure__form__input" disabled />
                                </div>
                                <div class="checkout-procedure__form d-flex flex-column gap-1 my-3">
                                    <label for="phone" class="checkout-procedure__form__label">Phone Number<span>&nbsp;*</span></label>
                                    <input type="text" placeholder="Your Phone Number" name="phone" id="phone" class="checkout-procedure__form__input" />
                                </div>
                                <div class="checkout-procedure__form d-flex flex-column gap-1 my-3">
                                    <label for="street" class="checkout-procedure__form__label">Street Address<span>&nbsp;*</span></label>
                                    <input type="text" placeholder="Your Address" name="street" id="street" class="checkout-procedure__form__input" />
                                </div>
                                <button type="button" class="checkout-procedure__btn-next" id="checkout-procedure__btn-next">
                                    Next
                                </button>
                            </fieldset>
                        </form>
                    </div>
                    <div class="col-4">
                        <div class="order-summary">
                            <h3 class="order-summary__title">Order Summary</h3>
                            <div class="order-summary__border border-bottom border-black border-opacity-25"></div>
                            <div class="order-summary__items">
                                <div class="order-summary__items__info d-flex py-3">
                                    <span class="order-summary__items__quantity"> 2 </span>
                                    <p class="order-summary__items__desc m-0">&nbsp;Items in Cart</p>
                                </div>
                                <ul class="order-summary__items__list list-unstyled m-0 d-flex flex-column gap-3">
                                    <li class="order-summary__items__list__product d-flex justify-content-between align-items-start gap-3">
                                        <img src="assets/img/custom-build_4.jpg" alt="product" class="object-fit-contain" />
                                        <div class="order-summary__items__list__info d-flex flex-column">
                                            <p class="order-summary__items__list__desc m-0">
                                                MSI MEG Trident X 10SD-1012AU Intel i7 10700K, 2070 SUPER...
                                            </p>
                                            <div class="order-summary__items__list__specs d-flex">
                                                <span class="order-summary__items__list__quantity">
                                                    <span class="order-summary__items__list__quantity__title">Qty&nbsp;</span>1</span>
                                                <span class="order-summary__items__list__price">&nbsp;&nbsp;$3,799.00</span>
                                            </div>
                                        </div>
                                    </li>
                                    <li class="order-summary__items__list__product d-flex justify-content-between align-items-start gap-3">
                                        <img src="assets/img/custom-build_4.jpg" alt="product" class="object-fit-contain" />
                                        <div class="order-summary__items__list__info d-flex flex-column">
                                            <p class="order-summary__items__list__desc m-0">
                                                MSI MEG Trident X 10SD-1012AU Intel i7 10700K, 2070 SUPER...
                                            </p>
                                            <div class="order-summary__items__list__specs d-flex">
                                                <span class="order-summary__items__list__quantity">
                                                    <span class="order-summary__items__list__quantity__title">Qty&nbsp;</span>1</span>
                                                <span class="order-summary__items__list__price">&nbsp;&nbsp;$3,799.00</span>
                                            </div>
                                        </div>
                                    </li>
                                    <li class="order-summary__items__list__product d-flex justify-content-between align-items-start gap-3">
                                        <img src="assets/img/custom-build_4.jpg" alt="product" class="object-fit-contain" />
                                        <div class="order-summary__items__list__info d-flex flex-column">
                                            <p class="order-summary__items__list__desc m-0">
                                                MSI MEG Trident X 10SD-1012AU Intel i7 10700K, 2070 SUPER...
                                            </p>
                                            <div class="order-summary__items__list__specs d-flex">
                                                <span class="order-summary__items__list__quantity">
                                                    <span class="order-summary__items__list__quantity__title">Qty&nbsp;</span>1</span>
                                                <span class="order-summary__items__list__price">&nbsp;&nbsp;$3,799.00</span>
                                            </div>
                                        </div>
                                    </li>
                                </ul>
                            </div>
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
                        <div class="cart-item">
                            <div class="row">
                                <div class="col-12">
                                    <div class="border-2 border-bottom border-black border-opacity-25 my-3"></div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-6">
                                    <div class="cart-item-container d-flex align-items-center">
                                        <img class="cart-item__img" src="assets/img/custom-build_3.jpg" alt="item" />
                                        <p class="cart-item__desc m-0">
                                            MSI MEG Trident X 10SD-1012AU Intel i7 10700K, 2070 SUPER, 32GB RAM, 1TB
                                            SSD, Windows 10 Home, Gaming Keyboard and Mouse 3 Years Warranty
                                        </p>
                                    </div>
                                </div>
                                <div class="col-2 d-flex align-items-center">
                                    <span class="cart-item__price">$4,349.00</span>
                                </div>
                                <div class="col-2 d-flex align-items-center">
                                    <input type="number" name="quantity" id="cart-quantity" min="1" value="1" class="cart-item__input-quantity" />
                                </div>
                                <div class="col-2 d-flex align-items-center">
                                    <span class="cart-item__price">$13,047.00</span>
                                </div>
                            </div>
                        </div>
                        <div class="cart-item">
                            <div class="row">
                                <div class="col-12">
                                    <div class="border-2 border-bottom border-black border-opacity-25 my-3"></div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-6">
                                    <div class="cart-item-container d-flex align-items-center">
                                        <img class="cart-item__img" src="assets/img/custom-build_3.jpg" alt="item" />
                                        <p class="cart-item__desc m-0">
                                            MSI MEG Trident X 10SD-1012AU Intel i7 10700K, 2070 SUPER, 32GB RAM, 1TB
                                            SSD, Windows 10 Home, Gaming Keyboard and Mouse 3 Years Warranty
                                        </p>
                                    </div>
                                </div>
                                <div class="col-2 d-flex align-items-center">
                                    <span class="cart-item__price">$4,349.00</span>
                                </div>
                                <div class="col-2 d-flex align-items-center">
                                    <input type="number" name="quantity" id="cart-quantity" min="1" value="1" class="cart-item__input-quantity" />
                                </div>
                                <div class="col-2 d-flex align-items-center">
                                    <span class="cart-item__price">$13,047.00</span>
                                </div>
                            </div>
                        </div>
                        <div class="cart-item">
                            <div class="row">
                                <div class="col-12">
                                    <div class="border-2 border-bottom border-black border-opacity-25 my-3"></div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-6">
                                    <div class="cart-item-container d-flex align-items-center">
                                        <img class="cart-item__img" src="assets/img/custom-build_3.jpg" alt="item" />
                                        <p class="cart-item__desc m-0">
                                            MSI MEG Trident X 10SD-1012AU Intel i7 10700K, 2070 SUPER, 32GB RAM, 1TB
                                            SSD, Windows 10 Home, Gaming Keyboard and Mouse 3 Years Warranty
                                        </p>
                                    </div>
                                </div>
                                <div class="col-2 d-flex align-items-center">
                                    <span class="cart-item__price">$4,349.00</span>
                                </div>
                                <div class="col-2 d-flex align-items-center">
                                    <input type="number" name="quantity" id="cart-quantity" min="1" value="1" class="cart-item__input-quantity" />
                                </div>
                                <div class="col-2 d-flex align-items-center">
                                    <span class="cart-item__price">$13,047.00</span>
                                </div>
                            </div>
                        </div>
                        <div class="cart-item-control">
                            <div class="row">
                                <div class="col-12">
                                    <div class="border-2 border-bottom border-black border-opacity-25 my-3"></div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12 d-flex justify-content-between">
                                    <div class="btn-control-first">
                                        <button class="continue-shopping">Continue Shopping</button>
                                        <button class="clear-cart">Clear Shopping Cart</button>
                                    </div>
                                    <div class="btn-control-second">
                                        <button class="update-cart">Update Shopping Cart</button>
                                    </div>
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
                                <img src="assets/img/zip.svg" alt="zip" class="checkout-summary__zip__img object-fit-contain" />
                                <img src="assets/img/vector.svg" alt="vector" class="checkout-summary__vector object-fit-contain" />
                                <p class="checkout-summary__zip__content m-0">up to 6 months interest free.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once "inc/components/footer.php"; ?>
<script src="<?php echo APP_URL; ?>/js/header/dropdown.js"></script>
<script src="<?php echo APP_URL; ?>/js/header/searchbar.js"></script>
<script>
    $("#checkout-progress").slick({
        slidesToShow: 1,
        slidesToScroll: 1,
    });

    const btnNext = document.querySelector("#checkout-procedure__btn-next");
    const progressCheckout = document.querySelector(".progress-container");
    const progress_first = progressCheckout.querySelector(".progress-bar-container:nth-child(1)");
    const progress_second = progressCheckout.querySelector(".progress-bar-container:nth-child(2)");

    btnNext.addEventListener("click", (e) => {
        // e.preventDefault();
        if (progress_first.classList.contains("loader")) {
            progress_first.classList.remove("loader");
            progress_first.classList.add("completed");
            progress_second.classList.add("active");
            progress_second.classList.add("loader");
        }
    });
</script>