<?php require_once "../inc/components/header.php"; ?>
<?php require_once "../inc/utils.php"; ?>
<?php
if (isset($_GET['verification_token']) && isset($_GET['email'])) {
    // Get otpId endcode from the request
    $verification_token = $_GET['verification_token'];
    $email = $_GET['email'];
    $forgot_password = '';

    if (isset($_GET['forgot_password'])) {
        if (base64_decode($_GET['forgot_password']) === FORGOT_PASSWORD)
            $forgot_password = $_GET['forgot_password'];
    }
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
                            <h3 class="title">Verification Account</h3>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="actions/verifyOTP.php" id="form-verify-otp" class="d-flex flex-column gap-4">
                                <div class="form-group d-flex flex-column gap-3">
                                    <label for="otp" class="fs-5">Enter OTP:</label>
                                    <input type="text" name="otp_code" class="form-control" id="otp" placeholder="Enter your OTP" required>
                                </div>
                                <div class="form-group d-flex flex-column gap-3">
                                    <button type="submit" class="btn btn-primary" id="verifyBtn">Verify</button>
                                    <button type="button" class="btn btn-secondary" id="resendBtn">Resend</button>
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
        $("#form-verify-otp").submit(async function(e) {
            e.preventDefault();
            const email = "<?php echo $email; ?>";
            const data = {
                email,
                verification_token: "<?php echo $verification_token; ?>",
                otp_code: $("#otp").val()
            }

            try {
                const verifyOtpResponse = await $.ajax({
                    method: "POST",
                    url: "actions/verifyOTP.php",
                    data
                })

                const {
                    status,
                    message
                } = JSON.parse(verifyOtpResponse);

                if (status) {
                    const forgotPassword = "<?php echo $forgot_password; ?>"
                    toastr.success("OTP is successfully verified", "OTP");
                    setTimeout(() => {
                        if (forgotPassword === '') {
                            window.location.replace(`<?php echo APP_URL; ?>/auth/login-register.php`);
                        } else {
                            window.location.replace(`<?php echo APP_URL; ?>/auth/reset-password.php?email=${email}`);
                        }
                    }, 1500)
                } else {
                    toastr.error(message, "Error");
                }
            } catch (error) {
                toastr.error(error.message, "Error");
            }
        })

        $("#resendBtn").click(async function(e) {
            e.preventDefault();
            const data = {
                email: "<?php echo $email; ?>"
            }
            $("#resendBtn").html("Loading...")
            try {
                const resendOtpResponse = await $.ajax({
                    method: "POST",
                    url: "actions/sendOTP.php",
                    data
                })
                $("#resendBtn").html("Resend")
                const response = JSON.parse(resendOtpResponse);
                if (response.status) {
                    const otpId = response.data.otp_id;
                    const email = response.data.email
                    const forgotPassword = "<?php echo $forgot_password; ?>"
                    toastr.success("Send OTP to email successfully", "Resend email")
                    setTimeout(() => {
                        if (forgotPassword === '') {
                            window.location.replace(`<?php echo APP_URL; ?>/auth/verification.php?verification_token=${otpId}&email=${email}`);
                        } else {
                            window.location.replace(`<?php echo APP_URL; ?>/auth/verification.php?verification_token=${otpId}&email=${email}&forgot_password=${forgotPassword}`);
                        }
                    }, 1500)
                } else {
                    toastr.warning(response.message, "User registration failed");
                }
            } catch (error) {
                toastr.error(error.message, "Error");
            }
        })
    })
</script>