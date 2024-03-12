<?php

require_once dirname(dirname(__DIR__)) . "/inc/init.php";
require_once dirname(dirname(__DIR__)) . "/inc/utils.php";
require_once dirname(dirname(__DIR__)) . "/classes/services/message.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['email']) || !isset($_POST['verification_token']) || !isset($_POST['otp_code']))
        return throwStatusMessage(Message::message(false, "Some information is missing"));

    $conn = require_once dirname(dirname(__DIR__)) . "/inc/db.php";
    $email = base64_decode($_POST['email']);
    $otp_id = base64_decode($_POST['verification_token']);
    $otp_code = $_POST['otp_code'];

    $user = User::getUserByEmail($conn, $email);

    if (empty($user)) return throwStatusMessage(Message::message(false, "User not found"));

    $verifyStatus = OTP::verifyOTP($conn, $otp_id, $otp_code);

    if (!$verifyStatus) return throwStatusMessage(Message::message(false, "Incorrect OTP code, please try again"));

    if (!User::updateUser($conn, $user->id, ['active' => 1])['status'])
        return throwStatusMessage(Message::message(false, "Your account does not validate. Please try again"));

    OTP::disableOtp($conn, $otp_id);
    return throwStatusMessage(Message::message(true, "OTP is verified, now you can log in"));
}
