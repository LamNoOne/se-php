<?php
require_once dirname(dirname(__DIR__)) . "/inc/init.php";
require_once dirname(dirname(__DIR__)) . "/inc/utils.php";
Auth::requireLogin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_conn))
        $conn = require_once dirname(dirname(__DIR__)) . "/inc/db.php";

    $uploadResult = UploadFile::process('file');

    if ($uploadResult['status'] !== UPLOAD_ERR_OK) {
        if ($uploadResult['status'] === UPLOAD_ERR_NO_FILE) {
            return throwStatusMessage(Message::message(false, 'Image is required'));
        }
        return throwStatusMessage(Message::message(false, $uploadResult['message']));
    }

    $userId = $_SESSION['userId'];
    $oldImage = $_SESSION['image'];
    $newImage = $uploadResult['url'];

    $response = User::updateUser($conn, $userId, ['imageUrl' => $newImage]);

    // set the new value for the session image
    if (!$response['status']) return throwStatusMessage(Message::message(false, "Cannot update user's image"));

    $_SESSION['image'] = $newImage;

    if($oldImage) {
        /**
         * $oldImage = "http://localhost//img1.jpg";
         * unlink not accept http
         * get the image name from the url -> use basename method instead
         */
        $filename = basename($oldImage);
        // delete oldFile
        unlink(dirname(dirname(__DIR__)) . "/uploads/" . $filename);
    }
    
    return throwStatusMessage($response);
}
