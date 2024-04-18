<?php

require_once dirname(dirname(__DIR__)) . "/inc/init.php";
require_once dirname(dirname(__DIR__)) . "/inc/utils.php";
require_once dirname(dirname(__DIR__)) . "/classes/services/message.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (empty($_POST['email']))
        return throwStatusMessage(Message::message(false, "Some information is missing"));

    $conn = require_once dirname(dirname(__DIR__)) . "/inc/db.php";

    $email = base64_decode($_POST['email']);

    $user = User::getUserByEmail($conn, $email);

    if (empty($user)) return throwStatusMessage(Message::message(false, "User not found"));

    $userId = $user->id;
    $email = $user->email;
    $firstName = $user->firstName;
    $lastName = $user->lastName;

    $otpCode = OTP::generateOTP();
    $opt = new OTP($userId, $otpCode);
    $optResponse = $opt->createOTP($conn);

    if (!$optResponse['status'])
        return throwStatusMessage(Message::message(false, 'Cannot create an OTP'));

    $subject = "Your verify code";
    $body = "<p>Dear $firstName $lastName </p> <h3>Your verify OTP code is $otpCode <br></h3>
            <p>Please verify your email to complete setup <br></p>
            <br><br>
            <b>SE Shop</b>";

    $responseEmail = Mail::sendEmail($email, $firstName . $lastName, $subject, $body);

    if (!$responseEmail['status']) return throwStatusMessage(Message::message(false, 'Send OTP failed'));
    return throwStatusMessage(Message::messageData(true, 'Send OTP to email successfully', ['email' => base64_encode($email), 'otp_id' => base64_encode($optResponse['data']['otpId'])]));
}
