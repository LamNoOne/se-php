<footer id="footer" class="bg-black pt-5">
    <div class="container">
        <div class="row">
            <div class="col">
                <h6 class="footer__title text-white-50">Infomation</h6>
                <ul class="footer__link list-unstyled">
                    <li>
                        <a href="#">About Us</a>
                    </li>
                    <li>
                        <a href="#">About Zip</a>
                    </li>
                    <li>
                        <a href="#">Privacy Policy</a>
                    </li>
                    <li>
                        <a href="#">Search</a>
                    </li>
                    <li>
                        <a href="#">Terms</a>
                    </li>
                    <li>
                        <a href="#">Orders and Returns</a>
                    </li>
                    <li>
                        <a href="#">Contact Us</a>
                    </li>
                    <li>
                        <a href="#">Advanced Search</a>
                    </li>
                    <li>
                        <a href="#">Newsletter Subscription</a>
                    </li>
                </ul>
            </div>
            <div class="col">
                <h6 class="footer__title text-white-50">PC Parts</h6>
                <ul class="footer__link list-unstyled">
                    <li>
                        <a href="#">CPUS</a>
                    </li>
                    <li>
                        <a href="#">Add On Cards </a>
                    </li>
                    <li>
                        <a href="#">Hard Drives (Internal) </a>
                    </li>
                    <li>
                        <a href="#">Graphic Cards</a>
                    </li>
                    <li>
                        <a href="#">Keyboards / Mice</a>
                    </li>
                    <li>
                        <a href="#">Cases / Power Supplies / Cooling</a>
                    </li>
                    <li>
                        <a href="#">RAM (Memory) </a>
                    </li>
                    <li>
                        <a href="#">Software</a>
                    </li>
                    <li>
                        <a href="#">Speakers / Headsets</a>
                    </li>
                    <li>
                        <a href="#">Motherboards</a>
                    </li>
                </ul>
            </div>
            <div class="col">
                <h6 class="footer__title text-white-50">Desktop PCs</h6>
                <ul class="footer__link list-unstyled">
                    <li>
                        <a href="#">Custom PCs</a>
                    </li>
                    <li>
                        <a href="#">Servers</a>
                    </li>
                    <li>
                        <a href="#">MSI All-In-One PCs </a>
                    </li>
                    <li>
                        <a href="#">HP/Compaq PCs </a>
                    </li>
                    <li>
                        <a href="#">ASUS PCs </a>
                    </li>
                    <li>
                        <a href="#">Tecs PCs</a>
                    </li>
                </ul>
            </div>
            <div class="col">
                <h6 class="footer__title text-white-50">Laptops</h6>
                <ul class="footer__link list-unstyled">
                    <li>
                        <a href="#">Everyday Use Notebooks </a>
                    </li>
                    <li>
                        <a href="#">MSI Workstation Series </a>
                    </li>
                    <li>
                        <a href="#">MSI Prestige Series </a>
                    </li>
                    <li>
                        <a href="#">Tablets and Pads</a>
                    </li>
                    <li>
                        <a href="#">Netbooks</a>
                    </li>
                    <li>
                        <a href="#">Infinity Gaming Notebooks</a>
                    </li>
                </ul>
            </div>
            <div class="col">
                <h6 class="footer__title text-white-50">Address</h6>
                <ul class="footer__link list-unstyled">
                    <li>
                        <a href="#">Address: 1234 Street Adress City Address, 1234</a>
                    </li>
                    <li>
                        <a href="#">Phones: (00) 1234 5678</a>
                    </li>
                    <li>
                        <a href="#">We are open: Monday-Thursday: 9:00 AM - 5:30 PM</a>
                    </li>
                    <li>
                        <a href="#">Friday: 9:00 AM - 6:00 PM</a>
                    </li>
                    <li>
                        <a href="#">Saturday: 11:00 AM - 5:00 PM</a>
                    </li>
                    <li>
                        <a href="#">E-mail: shop@email.com</a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <span class="d-block py-3" id="copy-right">Copyright © 2024 SE.</span>
</footer>
<script type="text/javascript" src="<?php echo APP_URL; ?>/js/header/logout.js"></script>
<script>
    $(document).ready(async function() {
        $(".btn-logout-confirm").on("click", async function(e) {
            e.preventDefault();

            try {
                // send request to logout php
                // at logout.php => call auth::logout() to destroy session
                const statusLogout = await $.ajax({
                    method: "POST",
                    url: "<?php echo APP_URL; ?>/auth/logout.php"
                })
                // after successfully logged out, navigate back to home page
                window.location.href = "<?php echo APP_URL; ?>";
            } catch (error) {
                // use toast to show error
                console.log(error);
            }
        });

        const logoutBtn = document.querySelector("#logout-btn");

        if (logoutBtn)
            logoutBtn.addEventListener("click", function(e) {
                e.preventDefault();
                const logoutPrimary = document.querySelector(".btn-logout-primary");
                logoutPrimary.click();
            });
    });
</script>
<script>
    const cartBtn =document.querySelector('#cart-btn');

    cartBtn.addEventListener('click', function(e) {
        e.preventDefault();
        window.location.href = "<?php echo APP_URL;?>/cart";
    })
</script>
</body>

</html>