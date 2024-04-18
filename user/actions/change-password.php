<?php 

require_once dirname(dirname(__DIR__)) . "/inc/init.php";
require_once dirname(dirname(__DIR__)). "/inc/utils.php";


if ($_SERVER['REQUEST_METHOD'] === 'POST' && Auth::isLoggedIn()) {
    if(!isset($conn))
        $conn = require_once dirname(dirname(__DIR__)). "/inc/db.php";

    if(!empty($_POST['oldPassword']) && !empty($_POST['newPassword'])) {
        $response = User::changeUserPassword($conn, $_SESSION['userId'], $_POST['oldPassword'], $_POST['newPassword']);
        return throwStatusMessage($response);
    }

    return throwStatusMessage(['status' => false, 'message' => "No data to change password"]);
}