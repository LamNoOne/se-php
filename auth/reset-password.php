<?php require_once "../inc/components/header.php"; ?>
<?php require_once "../inc/utils.php"; ?>

<?php
if ($_GET['email']) {
    $email = $_GET['email'];
} else {
    redirect(APP_URL);
}
?>

<div id="main-content" class="main-content">
    <div class="verification pb-5 pt-1">
        <div class="container mt-5">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="title">Password Recovery</h3>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="actions/sendOTP.php" id="form-recovery-password" class="d-flex flex-column gap-4">
                                <div class="form-group d-flex flex-column gap-3">
                                    <label for="password" class="fs-6">New Password:</label>
                                    <input type="password" name="password" class="form-control" id="password" placeholder="Enter your password" required>
                                    <label for="confirm_password" class="fs-6">Confirm Password:</label>
                                    <input type="password" name="confirm_password" class="form-control" id="confirm_password" placeholder="Confirm your password" required>
                                </div>
                                <div class="form-group d-flex flex-column gap-3">
                                    <button type="submit" class="btn btn-primary" id="reset_password">Reset Password</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<?php require_once "../inc/components/footer.php"; ?>

<script>
    $(document).ready(function() {
        // change user password
        const formChangePassword = $("#form-recovery-password");
        formChangePassword.validate({
            rules: {
                password: {
                    required: true,
                    minlength: 6,
                },
                confirm_password: {
                    required: true,
                    minlength: 6,
                    equalTo: "#password",
                },
            },
            messages: {
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

        formChangePassword.submit(async function(e) {
            e.preventDefault();

            const password = $("#password").val();
            const confirmPassword = $("#confirm_password").val();

            if (password !== confirmPassword) return;
            const email = "<?php echo $email ?>";

            const data = {
                email,
                password
            }

            try {
                const updatePasswordResponse = await $.ajax({
                    method: "POST",
                    url: "actions/reset-password.php",
                    data
                })

                const {
                    status,
                    message
                } = JSON.parse(updatePasswordResponse);

                if (status) {
                    toastr.success(message, "Update Password");
                    setTimeout(() => {
                        window.location.replace("<?php echo APP_URL; ?>/auth/login-register.php");
                    }, 1500)
                } else {
                    toastr.error(message, "Update Password");
                }
            } catch (error) {
                toastr.success(error.message, "Error");
            }
        })
    })
</script>