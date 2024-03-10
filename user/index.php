<?php require_once "../inc/components/header.php"; ?>

<?php

Auth::requireLogin();

if (!isset($conn))
    $conn = require_once "../inc/db.php";

$user = User::getUserById($conn, $_SESSION['userId']);
?>

<div id="customer-manager">
    <div class="container">
        <div class="row">
            <div class="col-2">
                <ul class="list-unstyled customer-manager__btn-list">
                    <li>
                        <a href="<?php echo APP_URL; ?>/user/" class="customer-manager__btn-list__item text-decoration-none text-black">
                            <img class="object-fit-contain" src="../assets/img/person.svg" alt="account">
                            <span>Your Account</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo APP_URL; ?>/user/order.php" class="customer-manager__btn-list__item text-decoration-none text-black">
                            <img class="object-fit-contain" src="../assets/img/history.svg" alt="history">
                            <span>Order History</span>
                        </a>
                    </li>
                    <li>
                        <button type="button" class="customer-manager__btn-list__item">
                            <img class="object-fit-contain" src="../assets/img/exit.svg" alt="logout">
                            <span>Log Out</span>
                        </button>
                    </li>
                </ul>
            </div>
            <div class="col-10 px-4">
                <div class="card">
                    <div class="card-header">Account Information</div>
                    <div class="card-body">
                        <!-- Avatar -->
                        <img src="<?php echo $user->imageUrl; ?>" alt="Avatar" id="avatar" class="img-thumbnail img-user object-fit-contain mb-3">
                        <!-- Change Avatar Form -->
                        <form id="changeAvatarForm" action="" method="" enctype="multipart/form-data">
                            <h5>Change Avatar</h5>
                            <!-- New Avatar -->
                            <div class="mb-3">
                                <label for="newAvatar" class="form-label">Upload New Avatar:</label>
                                <input type="file" name="file" id="newAvatar" class="form-control" accept="image/*">
                            </div>
                            <button type="button" id="submit-change-img" class="btn btn-primary">Change Avatar</button>
                        </form>
                        <form action="" id="update-user-info" enctype="multipart/form-data">
                            <!-- First Name -->
                            <div class="mb-3">
                                <label for="firstName" class="form-label">First Name:</label>
                                <input type="text" id="firstName" class="form-control" value="<?php echo $user->firstName; ?>" disabled readonly>
                            </div>
                            <!-- Last Name -->
                            <div class="mb-3">
                                <label for="lastName" class="form-label">Last Name:</label>
                                <input type="text" id="lastName" class="form-control" value="<?php echo $user->lastName; ?>" disabled readonly>
                            </div>
                            <!-- Phone Number -->
                            <div class="mb-3">
                                <label for="phoneNumber" class="form-label">Phone Number:</label>
                                <input type="tel" name="phoneNumber" id="phoneNumber" class="form-control" <?php echo isset($user->phoneNumber) ? "disabled" : "" ?> value="<?php echo isset($user->phoneNumber) ? $user->phoneNumber : ""  ?>">
                            </div>
                            <!-- Email -->
                            <div class="mb-3">
                                <label for="email" class="form-label">Email:</label>
                                <input type="email" id="email" class="form-control" value="<?php echo $user->email ?>" disabled readonly>
                            </div>
                            <!-- Address -->
                            <div class="mb-3">
                                <label for="address" class="form-label">Address:</label>
                                <textarea name="address" id="address" class="form-control" rows="3"><?php echo isset($user->address) ? $user->address : "" ?></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary">Update Info</button>
                        </form>
                        <!-- Change Password Form -->
                        <form id="change-user-password" method="POST" action="" enctype="multipart/form-data">
                            <h5>Change Password</h5>
                            <!-- Current Password -->
                            <div class="mb-3">
                                <label for="current-password" class="form-label">Current Password:</label>
                                <input type="password" id="current-password" name="currentPassword" class="form-control">
                            </div>
                            <!-- New Password -->
                            <div class="mb-3">
                                <label for="new-password" class="form-label">New Password:</label>
                                <input type="password" id="new-password" name="newPassword" class="form-control">
                            </div>
                            <!-- Confirm New Password -->
                            <div class="mb-3">
                                <label for="confirm-password" class="form-label">Confirm New Password:</label>
                                <input type="password" id="confirm-password" name="confirmPassword" class="form-control">
                            </div>
                            <button type="submit" class="btn btn-primary">Change Password</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once "../inc/components/footer.php"; ?>
<script src="<?php echo APP_URL; ?>/js/header/dropdown.js"></script>
<script src="<?php echo APP_URL; ?>/js/header/searchbar.js"></script>
<script>
    $(document).ready(function() {
        const submitBtnImage = $("#submit-change-img");
        const userImageBtn = $(".user__dropdown__image");
        const userImageChange = $(".img-user");

        const formUpdateUser = $("#update-user-info");
        const formChangePassword = $("#change-user-password");

        const uploadImage = async function(formData) {
            try {
                const uploadStatus = await $.ajax({
                    url: "actions/upload-image.php",
                    type: "POST",
                    data: formData,
                    contentType: false,
                    processData: false,
                })

                return JSON.parse(uploadStatus);
            } catch (error) {
                return {
                    status: false,
                    message: error.message
                };
            }
        }

        // update user image
        submitBtnImage.click(async function() {
            const currFile = $("#newAvatar")[0].files;
            if (currFile.length === 0) return;
            const formData = new FormData();

            formData.append("file", currFile[0]);
            const {
                status,
                message
            } = await uploadImage(formData);

            if (status) {
                toastr.success(message, "Update user's image");
                setTimeout(() => {
                    window.location.reload();
                }, 1000);
            } else {
                toastr.error(message, "Error");
            }
        })

        jQuery.validator.addMethod("valid_phone", function(value) {
            const regex = /(84|0[3|5|7|8|9])+([0-9]{8})\b/g;
            return value.trim().match(regex);
        });

        formUpdateUser.validate({
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

        // update user information
        formUpdateUser.submit(async function(event) {
            event.preventDefault();

            const data =
                $("#phoneNumber").attr("disabled") ? {
                    address: $("#address").val()
                } : {
                    phoneNumber: $("#phoneNumber").val(),
                    address: $("#address").val()
                }

            console.log(data);

            try {
                const updateUserResponse = await $.ajax({
                    method: "POST",
                    url: "actions/update-info.php",
                    data: data,
                })

                const {
                    status,
                    message
                } = JSON.parse(updateUserResponse);

                if (status) {
                    toastr.success(message, "Update User");
                    setTimeout(() => {
                        window.location.reload();
                    }, 1000);
                } else {
                    toastr.warning(message, "Update User");
                }

            } catch (error) {
                toastr.error(error.message, "Update User");
            }
        })


        // change user password
        formChangePassword.validate({
            rules: {
                currentPassword: {
                    required: true,
                    minlength: 6,
                },
                newPassword: {
                    required: true,
                    minlength: 6,
                },
                confirmPassword: {
                    required: true,
                    minlength: 6,
                    equalTo: "#new-password",
                },
            },
            messages: {
                currentPassword: {
                    required: "Please provide a password",
                    minlength: "Your password must be at least 6 characters long",
                },
                newPassword: {
                    required: "Please provide a password",
                    minlength: "Your password must be at least 6 characters long",
                },
                confirmPassword: {
                    required: "Please provide a password",
                    minlength: "Your password must be at least 6 characters long",
                    equalTo: "Please enter the same password as above",
                },
            },
        });

        formChangePassword.submit(async function(event) {
            event.preventDefault();

            const currentPassword = $("#current-password").val();
            const newPassword = $("#new-password").val();
            const confirmPassword = $("#confirm-password").val();
            if (newPassword != confirmPassword) return;

            const passwordData = {
                oldPassword: currentPassword,
                newPassword: newPassword
            }
            try {
                const updatePasswordResponse = await $.ajax({
                    method: "POST",
                    url: "actions/change-password.php",
                    data: passwordData
                })

                const {
                    status,
                    message
                } = JSON.parse(updatePasswordResponse);

                status ? toastr.success(message, "Update Password") : toastr.error(message, "Update Password");
            } catch (error) {
                toastr.error(error.message, "Invalid password");
            }
        })
    })
</script>