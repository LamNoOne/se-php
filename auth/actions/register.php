<?php

require_once dirname(dirname(__DIR__)) . "/inc/init.php";
require_once dirname(dirname(__DIR__)) . "/inc/utils.php";
require_once dirname(dirname(__DIR__)) . "/classes/services/message.php";

$errorMsg = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($conn))
        $conn = require dirname(dirname(__DIR__)) . "/inc/db.php";

    $firstName = $_POST['firstName'];
    if (strlen($firstName) < 2) {
        $errorMsg = "FirstName is invalid";
        return throwStatusMessage(Message::message(false, $errorMsg));
    }

    $lastName = $_POST['lastName'];
    if (strlen($lastName) < 2) {
        $errorMsg = "LastName is invalid";
        return throwStatusMessage(Message::message(false, $errorMsg));
    }

    $username = $_POST['username'];
    if (strlen($username) < 6) {
        $errorMsg = "Username is invalid";
        return throwStatusMessage(Message::message(false, $errorMsg));
    }

    $email = $_POST['email'];
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errorMsg = "Invalid email";
        return throwStatusMessage(Message::message(false, $errorMsg));
    }

    $password = $_POST['password'];
    if (strlen($password) < 6) {
        $errorMsg = "Password is invalid";
        return throwStatusMessage(Message::message(false, $errorMsg));
    }


    $user = new User($lastName, $firstName, $username, $email, $password);
    $userResponse = $user->createUser($conn);

    if (empty($userResponse['data']['userId']))
        return throwStatusMessage(Message::message(false, 'Cannot create this user'));

    // sending email to verified user
    $userId = $userResponse['data']['userId'];
    $otpCode = OTP::generateOTP();
    $opt = new OTP($userId, $otpCode);
    $optResponse = $opt->createOTP($conn);

    if (!$optResponse['status'])
        return throwStatusMessage(Message::message(false, 'Cannot create an OTP'));

    $subject = "Your verify code";
    $body = "<p>Dear $firstName $lastName, </p> <h3>Your verify OTP code is $otpCode <br></h3>
            <br><br>
            <p>With regrads,</p>
            <b>Programming with Lam</b>
            https://github.com/LamNoOne";

    $responseEmail = Mail::sendEmail($email, $firstName . " " . $lastName, $subject, $body);

    if (!$responseEmail['status']) return throwStatusMessage(Message::message(false, 'Verify your email failed'));
    return throwStatusMessage(Message::messageData(true, 'Create OTP successfully', ['email' => base64_encode($email), 'otp_id' => base64_encode($optResponse['data']['otpId'])]));
}
