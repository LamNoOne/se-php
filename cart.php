<?php require_once "inc/components/header.php"; ?>
<div id="main-content" class="main-content">
    <div id="shopping-cart">
        <div class="container">
            <div class="row">
                <h1 class="shopping-cart__title mb-4">Shopping Cart</h1>
            </div>
            <div class="row">
                <div class="col-9">
                    <div class="row">
                        <div class="col-5">
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
                        <div class="col-1">
                            <span class="cart-header"></span>
                        </div>
                    </div>
                    <div class="cart-item">
                        <div class="row">
                            <div class="col-12">
                                <div class="border-2 border-bottom border-black border-opacity-25 my-3"></div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-5">
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
                            <div class="col-1 d-flex align-items-center justify-content-end">
                                <button type="button" class="cart-item__btn-delete-cart"></button>
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
                            <div class="col-5">
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
                            <div class="col-1 d-flex align-items-center justify-content-end">
                                <button type="button" class="cart-item__btn-delete-cart"></button>
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
                            <div class="col-5">
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
                            <div class="col-1 d-flex align-items-center justify-content-end">
                                <button type="button" class="cart-item__btn-delete-cart"></button>
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
                <div class="col-3">
                    <div class="checkout-summary">
                        <h3 class="checkout-summary__header">Summary</h3>
                        <div class="checkout-summary__block d-flex justify-content-between my-2">
                            <p class="checkout-summary__title m-0">Subtotal</p>
                            <span class="checkout-summary__value">$13,047.00</span>
                        </div>
                        <!-- <div class="checkout-summary__block d-flex justify-content-between my-2">
                                <p class="checkout-summary__title m-0">Shipping</p>
                                <span class="checkout-summary__value">Free</span>
                            </div>
                            <div class="checkout-summary__block d-flex justify-content-between my-2">
                                <p class="checkout-summary__note m-0">
                                    (Standard Rate - Price may vary depending on the item/destination. TECS Staff will
                                    contact you.)
                                </p>
                            </div> -->
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
<?php require_once "inc/components/footer.php"; ?>
<script src="<?php echo APP_URL; ?>/js/header/dropdown.js"></script>
<script src="<?php echo APP_URL; ?>/js/header/searchbar.js"></script>