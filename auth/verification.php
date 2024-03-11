<?php require_once "../inc/components/header.php"; ?>
<?php require_once "../inc/utils.php"; ?>

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
                            <form class="d-flex flex-column gap-4">
                                <div class="form-group d-flex flex-column gap-3">
                                    <label for="otp" class="fs-5">Enter OTP:</label>
                                    <input type="text" class="form-control" id="otp" placeholder="Enter your OTP" required>
                                </div>
                                <div class="form-group d-flex flex-column gap-3">
                                    <button type="button" class="btn btn-primary" id="verifyBtn">Verify</button>
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