<?php

require_once dirname(dirname(__DIR__)) . "/inc/init.php";
require_once dirname(dirname(__DIR__)) . "/inc/utils.php";
require_once dirname(dirname(__DIR__)) . "/classes/services/message.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (empty($_POST['email']) || empty($_POST['password'])) {
        // Display toast message warning
    } else {
        if (!isset($conn))
            $conn = require_once dirname(dirname(__DIR__)) . "/inc/db.php";
        $email = $_POST['email'];
        $password = $_POST['password'];

        $data = User::authenticate($conn, $email, $password);

        if ($data['status']) {

            $message = $data['message'];
            $user = $data['data'];

            // If user has not one time verified
            if (!User::isVerifiedAccount($conn, $user->id)) {
                $userId = $user->id;
                // create new OTP
                $otpCode = OTP::generateOTP();
                // Add new OTP to database
                $opt = new OTP($userId, $otpCode);
                $optResponse = $opt->createOTP($conn);

                if (!$optResponse['status'])
                    return throwStatusMessage(Message::message(false, 'Cannot create an OTP'));

                $firstName = $user->firstName;
                $lastName = $user->lastName;
                $subject = "Your verify code";
                $body = "<p>Dear $firstName $lastName </p> <h3>Your verify OTP code is $otpCode <br></h3>
                            <p>Please verify your email to complete setup <br></p>
                            <br><br>
                            <b>SE Shop</b>";

                $responseEmail = Mail::sendEmail($email, $firstName . $lastName, $subject, $body);

                if (!$responseEmail['status']) return throwStatusMessage(Message::message(false, 'Send OTP failed'));
                return throwStatusMessage(Message::messageData(true, 'Send OTP to email successfully', ['redirect' => APP_URL . "/auth/verification.php?verification_token=" . base64_encode($optResponse['data']['otpId']) . "&email=" . base64_encode($email)]));
            }
            $_SESSION['username'] = $user->username;
            $_SESSION['firstName'] = $user->firstName;
            $_SESSION['lastName'] = $user->lastName;
            $_SESSION['email'] = $user->email;
            $_SESSION['userId'] = $user->id;
            $_SESSION['image'] = $user->imageUrl;
            $_SESSION['roleId'] = $user->roleId;
            if ($user->phoneNumber !== NULL) {
                $_SESSION['phoneNumber'] = $user->phoneNumber;
            }
            if ($user->address !== NULL) {
                $_SESSION['address'] = $user->address;
            }
            if (isset($_SESSION['userId']))
                Auth::login();
            return throwStatusMessage(Message::messageData(true, $message, ['redirect' => APP_URL]));
        } else {
            return throwStatusMessage(Message::message(false, "Invalid username or password"));
        }
    }
}
