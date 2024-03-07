<?php 

require_once dirname(dirname(__DIR__)) . "/inc/init.php";
require_once dirname(dirname(__DIR__)). "/inc/utils.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && Auth::isLoggedIn()) {

    $data = array();

    if(!empty($_POST['phoneNumber'])) {
        $data['phoneNumber'] = $_POST['phoneNumber'];
    }

    if(!empty($_POST['address'])) {
        $data['address'] = $_POST['address'];
    }

    if(!empty($data)) {
        if(!isset($conn))
            $conn = require_once dirname(dirname(__DIR__)) . "/inc/db.php";
        $response = User::updateUser($conn, $_SESSION['userId'], $data);
        if($response['status']) {
            if(!empty($data['phoneNumber']))
                $_SESSION['phoneNumber'] = $data['phoneNumber'];
            if(!empty($data['address']))
                $_SESSION['address'] = $data['address'];
        }
        return throwStatusMessage($response);
    }

    return throwStatusMessage(['status' => false, 'message' => "Invalid data to update user"]);
}