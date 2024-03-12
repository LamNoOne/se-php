<?php require_once "../inc/components/header.php"; ?>
<?php require_once "../inc/utils.php"; ?>

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
                            <form method="POST" action="actions/resendOTP.php" id="form-recovery-password" class="d-flex flex-column gap-4">
                                <div class="form-group d-flex flex-column gap-3">
                                    <label for="otp" class="fs-5">Enter Email:</label>
                                    <input type="text" name="email" class="form-control" id="email" placeholder="Enter your email" required>
                                </div>
                                <div class="form-group d-flex flex-column gap-3">
                                    <button type="submit" class="btn btn-primary" id="verifyBtn">Send</button>
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
        $("#form-recovery-password").submit(async function(e) {
            e.preventDefault();
            const data = {
                email: $("#email").val(),
            }

            try {
                const verifyOtpResponse = await $.ajax({
                    method: "POST",
                    url: "actions/resendOTP.php",
                    data
                })

                const {
                    status,
                    message
                } = JSON.parse(verifyOtpResponse);

                status ? window.location.replace("<?php echo APP_URL; ?>/auth/login-register.php") : toastr.error(message, "Error");
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
                    url: "actions/resendOTP.php",
                    data
                })
                $("#resendBtn").html("Resend")
                const response = JSON.parse(resendOtpResponse);
                if (response.status) {
                    const otpId = response.data.otp_id;
                    const email = response.data.email
                    window.location.replace(`<?php echo APP_URL; ?>/auth/verification.php?verification_token=${otpId}&email=${email}`);
                } else {
                    toastr.warning(message, "User registration failed");
                }
            } catch (error) {
                toastr.error(error.message, "Error");
            }
        })
    })
</script>