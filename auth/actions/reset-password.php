<?php

require_once dirname(dirname(__DIR__)) . "/inc/init.php";
require_once dirname(dirname(__DIR__)) . "/inc/utils.php";
require_once dirname(dirname(__DIR__)) . "/classes/services/message.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (empty($_POST['email']) && empty($_POST['password']))
        return throwStatusMessage(Message::message(false, "Some information is missing"));

    $conn = require_once dirname(dirname(__DIR__)) . "/inc/db.php";

    $email = base64_decode($_POST['email']);
    $password = $_POST['password'];

    $passwordResponse = User::changeUserPasswordByEmail($conn, $email, $password);
    if (!$passwordResponse['status'])
        return throwStatusMessage(Message::message(false, $passwordResponse['message']));

    return throwStatusMessage(Message::message(true, $passwordResponse['message']));
}
