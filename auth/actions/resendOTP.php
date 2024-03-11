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

    if (is_array($user)) {
        return throwStatusMessage(Message::message(false, $user['message']));
    }

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
            <br><br>
            <p>With regrads,</p>
            <b>Programming with Lam</b>
            https://github.com/LamNoOne";

    $responseEmail = Mail::sendEmail($email, $firstName . $lastName, $subject, $body);

    if (!$responseEmail['status']) return throwStatusMessage(Message::message(false, 'Verify your email failed'));
    return throwStatusMessage(Message::messageData(true, 'Create OTP successfully', ['email' => base64_encode($email), 'otp_id' => base64_encode($optResponse['data']['otpId'])]));
}
