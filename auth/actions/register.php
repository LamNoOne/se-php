<?php

require_once dirname(dirname(__DIR__)) . "/inc/init.php";
require_once dirname(dirname(__DIR__)). "/inc/utils.php";

$errorMsg = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($conn))
        $conn = require dirname(dirname(__DIR__)) . "/inc/db.php";

    $firstName = $_POST['firstName'];
    if (strlen($firstName) < 2) {
        $errorMsg = "FirstName is invalid";
        return throwStatusMessage(['status' => false, 'message' => $errorMsg]);
    }

    $lastName = $_POST['lastName'];
    if (strlen($lastName) < 2) {
        $errorMsg = "LastName is invalid";
        return throwStatusMessage(['status' => false, 'message' => $errorMsg]);
    }

    $username = $_POST['username'];
    if (strlen($username) < 6) {
        $errorMsg = "Username is invalid";
        return throwStatusMessage(['status' => false, 'message' => $errorMsg]);
    }

    $email = $_POST['email'];
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errorMsg = "Invalid email";
        return throwStatusMessage(['status' => false,'message' => $errorMsg]);
    }

    $password = $_POST['password'];
    if (strlen($password) < 6) {
        $errorMsg = "Password is invalid";
        return throwStatusMessage(['status' => false,'message' => $errorMsg]);
    }


    $user = new User($lastName, $firstName, $username, $email, $password);
    $userResponse = $user->createUser($conn);
    return throwStatusMessage($userResponse);
}
