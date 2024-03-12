<?php require_once "../inc/components/header.php"; ?>
<?php require_once "../inc/utils.php"; ?>

<div id="main-content" class="main-content">
    <div class="verification pb-5 pt-1">
        <div class="container mt-5">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="title">OTP Email Recovery</h3>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="actions/sendOTP.php" id="form-recovery-password" class="d-flex flex-column gap-4">
                                <div class="form-group d-flex flex-column gap-3">
                                    <label for="email" class="fs-5">Enter Email:</label>
                                    <input type="email" name="email" class="form-control" id="email" placeholder="Enter your email" required>
                                </div>
                                <div class="form-group d-flex flex-column gap-3">
                                    <button type="submit" class="btn btn-primary" id="sendEmail">Send</button>
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
                email: btoa($("#email").val()),
            }
            $("#sendEmail").html("Sending...");

            try {
                const sendOTPEmailResponse = await $.ajax({
                    method: "POST",
                    url: "actions/sendOTP.php",
                    data
                })

                const response = JSON.parse(sendOTPEmailResponse);

                if (response.status) {
                    const otpId = response.data.otp_id;
                    const email = response.data.email
                    const forgotPassword = btoa("<?php echo FORGOT_PASSWORD; ?>");
                    toastr.success(response.message, "Email Sent");
                    setTimeout(() => {
                        window.location.replace(`<?php echo APP_URL; ?>/auth/verification.php?verification_token=${otpId}&email=${email}&forgot_password=${forgotPassword}`);
                    }, 1500)
                } else {
                    toastr.error(response.message, "Error");
                    $("#sendEmail").html("Send");

                }
            } catch (error) {
                toastr.error(error.message, "Error");
            }
        })
        $("#sendEmail").html("Send");
    })
</script>