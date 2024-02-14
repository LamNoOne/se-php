<?php require_once "../inc/components/header.php"; ?>

<div id="customer-manager">
    <div class="container">
        <div class="row">
            <div class="col-2">
                <ul class="list-unstyled customer-manager__btn-list">
                    <li>
                        <button type="button" class="customer-manager__btn-list__item">
                            <img class="object-fit-contain" src="../assets/img/person.svg" alt="account">
                            <span>Your Account</span>
                        </button>
                    </li>
                    <li>
                        <button type="button" class="customer-manager__btn-list__item">
                            <img class="object-fit-contain" src="../assets/img/history.svg" alt="history">
                            <span>Order History</span>
                        </button>
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
                        <img src="avatar.jpg" alt="Avatar" id="avatar" class="img-thumbnail mb-3">
                        <!-- Change Avatar Form -->
                        <form id="changeAvatarForm">
                            <h5>Change Avatar</h5>
                            <!-- New Avatar -->
                            <div class="mb-3">
                                <label for="newAvatar" class="form-label">Upload New Avatar:</label>
                                <input type="file" id="newAvatar" class="form-control" accept="image/*">
                            </div>
                            <button type="button" class="btn btn-primary" onclick="changeAvatar()">Change Avatar</button>
                        </form>
                        <!-- First Name -->
                        <div class="mb-3">
                            <label for="firstName" class="form-label">First Name:</label>
                            <input type="text" id="firstName" class="form-control" value="John" readonly>
                        </div>
                        <!-- Last Name -->
                        <div class="mb-3">
                            <label for="lastName" class="form-label">Last Name:</label>
                            <input type="text" id="lastName" class="form-control" value="Doe" readonly>
                        </div>
                        <!-- Phone Number -->
                        <div class="mb-3">
                            <label for="phoneNumber" class="form-label">Phone Number:</label>
                            <input type="tel" id="phoneNumber" class="form-control" value="123-456-7890" readonly>
                        </div>
                        <!-- Email -->
                        <div class="mb-3">
                            <label for="email" class="form-label">Email:</label>
                            <input type="email" id="email" class="form-control" value="john@example.com" readonly>
                        </div>
                        <!-- Address -->
                        <div class="mb-3">
                            <label for="address" class="form-label">Address:</label>
                            <textarea id="address" class="form-control" rows="3" readonly>123 Street, City, Country</textarea>
                        </div>
                        <!-- Change Password Form -->
                        <form id="changePasswordForm">
                            <h5>Change Password</h5>
                            <!-- Current Password -->
                            <div class="mb-3">
                                <label for="currentPassword" class="form-label">Current Password:</label>
                                <input type="password" id="currentPassword" class="form-control" required>
                            </div>
                            <!-- New Password -->
                            <div class="mb-3">
                                <label for="newPassword" class="form-label">New Password:</label>
                                <input type="password" id="newPassword" class="form-control" required>
                            </div>
                            <!-- Confirm New Password -->
                            <div class="mb-3">
                                <label for="confirmPassword" class="form-label">Confirm New Password:</label>
                                <input type="password" id="confirmPassword" class="form-control" required>
                            </div>
                            <button type="button" class="btn btn-primary" onclick="changePassword()">Change Password</button>
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