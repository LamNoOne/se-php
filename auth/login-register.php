<?php require_once "../inc/components/header.php"; ?>
<?php require_once "../inc/utils.php"; ?>

<?php
if (isset($_SESSION['username'])) {
    redirect(APP_URL);
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (empty($_POST['email']) || empty($_POST['password'])) {
        // Display toast message warning
    } else {
        if (!isset($conn))
            $conn = require_once "../inc/db.php";
        $email = $_POST['email'];
        $password = $_POST['password'];

        $data = User::authenticate($conn, $email, $password);

        if ($data['status']) {
            $message = $data['message'];
            $user = $data['data'];
            $_SESSION['username'] = $user->username;
            $_SESSION['firstName'] = $user->firstName;
            $_SESSION['lastName'] = $user->lastName;
            $_SESSION['email'] = $user->email;
            $_SESSION['userId'] = $user->id;
            $_SESSION['image'] = $user->imageUrl;
            if ($user->phoneNumber !== NULL) {
                $_SESSION['phoneNumber'] = $user->phoneNumber;
            }
            if ($user->address !== NULL) {
                $_SESSION['address'] = $user->address;
            }
            if (isset($_SESSION['userId']))
                Auth::login();
            redirect(APP_URL);
        } else {
            // Display toast message warning
            // Cannot display error message when wrong username or password, move logic to login.php later
            $status = $data['status'];
            $message = $data['message'];
        }
    }
}
?>

<div id="customer-login-register">
    <div class="container">
        <div class="row">
            <div class="col">
                <h1 class="customer-login-register__title">Customer Login</h1>
                <h1 class="customer-login-register__title d-none">Customer Register</h1>
            </div>
        </div>
        <div class="row">
            <div class="d-sm-none d-lg-block col-lg-1"></div>
            <div class="col-sm-12 col-lg-5">
                <div id="login-register-container">
                    <div class="login-container p-5">
                        <h3 class="login-container__title mb-2">Registered Customers</h3>
                        <p class="login-container__desc m-0">
                            If you have an account, sign in with your email address.
                        </p>
                        <form action="login-register.php" method="post" class="login-form">
                            <fieldset>
                                <div class="login-form__input-container d-flex flex-column gap-1 my-3">
                                    <label for="email-login" class="login-form__input__label">Email<span>&nbsp;*</span></label>
                                    <input type="email" placeholder="Your Email" name="email" id="email-login" class="login-form__input" />
                                </div>
                                <div class="login-form__input-container d-flex flex-column gap-1 my-3">
                                    <label for="password-login" class="login-form__input__label">Password<span>&nbsp;*</span></label>
                                    <input type="password" placeholder="Your Password" name="password" id="password-login" class="login-form__input" />
                                </div>
                                <div class="login-form__submit-container d-flex align-items-center justify-content-between my-4">
                                    <button type="submit" class="login-form__submit-container__submit">
                                        Sign In
                                    </button>
                                    <a class="login-form__submit-container__forgot-password" href="#">Forgot Your Password?</a>
                                </div>
                            </fieldset>
                        </form>
                    </div>
                    <div class="login-container p-5">
                        <h3 class="login-container__title mb-2">Customers Register</h3>
                        <p class="login-container__desc m-0">Please sign up to access our services:</p>
                        <form action="actions/register.php" method="POST" id="form-register" class="login-form">
                            <fieldset>
                                <div class="login-form__input-container d-flex flex-column gap-1 my-3">
                                    <label for="firstname" class="login-form__input__label">First Name:<span>&nbsp;*</span></label>
                                    <input type="text" placeholder="Ex: John" name="firstName" id="firstname" class="login-form__input" />
                                </div>
                                <div class="login-form__input-container d-flex flex-column gap-1 my-3">
                                    <label for="lastname" class="login-form__input__label">Last Name:<span>&nbsp;*</span></label>
                                    <input type="text" placeholder="Ex: William" name="lastName" id="lastname" class="login-form__input" />
                                </div>
                                <div class="login-form__input-container d-flex flex-column gap-1 my-3">
                                    <label for="username" class="login-form__input__label">Username:<span>&nbsp;*</span></label>
                                    <input type="text" placeholder="username" name="username" id="username" class="login-form__input" />
                                </div>
                                <div class="login-form__input-container d-flex flex-column gap-1 my-3">
                                    <label for="email-register" class="login-form__input__label">Email<span>&nbsp;*</span></label>
                                    <input type="email" placeholder="example@gmail.com" name="email" id="email-register" class="login-form__input" />
                                </div>
                                <div class="login-form__input-container d-flex flex-column gap-1 my-3">
                                    <label for="password-register" class="login-form__input__label">Password<span>&nbsp;*</span></label>
                                    <input type="password" placeholder="Your Password" name="password" id="password-register" class="login-form__input" />
                                </div>
                                <div class="login-form__input-container d-flex flex-column gap-1 my-3">
                                    <label for="confirm_password" class="login-form__input__label">Confirm Password<span>&nbsp;*</span></label>
                                    <input type="password" placeholder="Confirm Password" name="confirm_password" id="confirm_password" class="login-form__input" />
                                </div>
                                <button type="submit" id="submit-register" class="login-form__submit-container__submit my-4">
                                    Create an Account
                                </button>
                            </fieldset>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-sm-12 col-lg-5">
                <div class="login-container p-5 login-container__right">
                    <h3 class="login-container__title mb-2">New Customer?</h3>
                    <p class="login-container__desc m-0">Creating an account has many benefits:</p>
                    <ul>
                        <li>
                            <p class="login-container__desc m-0">Check out faster</p>
                        </li>
                        <li>
                            <p class="login-container__desc m-0">Keep more than one address</p>
                        </li>
                        <li>
                            <p class="login-container__desc m-0">Track orders and more</p>
                        </li>
                    </ul>
                    <div class="dots-append-login-container"></div>
                    <div class="login-question-container d-flex justify-content-between align-items-center d-none">
                        <a class="login-question" href="#">Already have an account?</a>
                        <button class="login-form__submit-container__submit">Sign In</button>
                    </div>
                </div>
            </div>
            <div class="d-sm-none d-lg-block col-lg-1"></div>
        </div>
    </div>
</div>

<?php require_once "../inc/components/footer.php"; ?>
<!-- <script src="<?php echo APP_URL; ?>/js/header/dropdown.js"></script> -->
<!-- <script src="<?php echo APP_URL; ?>/js/header/searchbar.js"></script> -->
<script>
    $("#login-register-container").slick({
        slidesToShow: 1,
        slidesToScroll: 1,
        arrows: false,
        draggable: false,
        fade: true,
        appendDots: $(".dots-append-login-container"),
        dots: true,
        dotsClass: "dots-append",
    });

    const signInContainer = document.querySelector(".dots-append-login-container .dots-append li:nth-child(1)");
    const registerContainer = document.querySelector(".dots-append-login-container .dots-append li:nth-child(2)");
    const signInBtn = document.querySelector(".dots-append-login-container .dots-append li:nth-child(1) button");
    const registerBtn = document.querySelector(".dots-append-login-container .dots-append li:nth-child(2) button");

    registerBtn.textContent = "Create an Account";
    const spanRegister = document.createElement("span");
    spanRegister.textContent = "Please sign up to use our services:";
    registerContainer.appendChild(spanRegister);

    signInBtn.textContent = "Sign In";
    const spanSignIn = document.createElement("span");
    spanSignIn.textContent = "If you have an account, sign in:";
    signInContainer.appendChild(spanSignIn);

    if (signInContainer.classList.contains("slick-active")) {
        signInContainer.classList.add("d-none");
    }

    // check if button is clicked, hide and display another one
    if (signInBtn)
        signInBtn.addEventListener("click", () => {
            signInContainer.classList.add("d-none");
            if (registerContainer.classList.contains("d-none")) {
                registerContainer.classList.remove("d-none");
            }
        });
    // check if button is clicked, hide and display another one
    if (registerBtn)
        registerBtn.addEventListener("click", () => {
            registerContainer.classList.add("d-none");
            if (signInContainer.classList.contains("d-none")) {
                signInContainer.classList.remove("d-none");
            }
        });
</script>

<script>
    $(document).ready(function() {
        const formRegister = $("#form-register");

        jQuery.validator.addMethod("valid_email", function(value) {
            const regex = /^[a-z0-9]+([-._][a-z0-9]+)*@([a-z0-9]+(-[a-z0-9]+)*\.)+[a-z]{1,5}$/;
            return value.trim().match(regex);
        });

        formRegister.validate({
            rules: {
                firstName: {
                    required: true,
                    minlength: 2,
                },
                lastName: {
                    required: true,
                    minlength: 2,
                },
                username: {
                    required: true,
                    minlength: 6,
                },
                email: {
                    required: true,
                    valid_email: true,
                },
                password: {
                    required: true,
                    minlength: 6,
                },
                confirm_password: {
                    required: true,
                    minlength: 6,
                    equalTo: "#password-register",
                },
            },
            messages: {
                firstName: {
                    required: "Please enter your first name",
                    minlength: "Your first name must consist of at least 2 characters",
                },
                lastName: {
                    required: "Please enter your last name",
                    minlength: "Your last name must consist of at least 2 characters",
                },
                username: {
                    required: "Please enter your username",
                    minlength: "Your username must consist of at least 6 characters",
                },
                email: {
                    required: "Please enter your email",
                    valid_email: "Please enter a valid email address!"
                },
                password: {
                    required: "Please provide a password",
                    minlength: "Your password must be at least 6 characters long",
                },
                confirm_password: {
                    required: "Please provide a password",
                    minlength: "Your password must be at least 6 characters long",
                    equalTo: "Please enter the same password as above",
                },
            },
        });

        formRegister.submit(async function(e) {
            e.preventDefault();
            const formData = {
                firstName: $("#firstname").val(),
                lastName: $("#lastname").val(),
                username: $("#username").val(),
                email: $("#email-register").val(),
                password: $("#password-register").val(),
            }

            try {
                const registerResponse = await $.ajax({
                    method: "POST",
                    url: "actions/register.php",
                    data: formData,
                })

                const {
                    status,
                    message
                } = JSON.parse(registerResponse);

                status ? toastr.success(message, "Register User") :
                    toastr.warning(message, "User registration failed");
            } catch (error) {
                toastr.error(message, "Registration error");
            }
        })
    })
</script>