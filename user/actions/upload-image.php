<?php
require_once dirname(dirname(__DIR__)) . "/inc/init.php";
require_once dirname(dirname(__DIR__)) . "/inc/utils.php";
Auth::requireLogin();

function upload_file()
{
    try {
        if (empty($_FILES)) {
            return Message::message(false, 'Can not upload files');
        }

        $rs = Errorfileupload::err($_FILES['file']['error']);
        if ($rs != 'OK') {
            return Message::message(false, $rs);
        }

        $fileMaxSize = FILE_MAX_SIZE;
        if ($_FILES['file']['size'] > $fileMaxSize) {
            return Message::message(false, 'File too large, must smaller than: ' .  $fileMaxSize);
        }

        // limit file image type
        $mime_types = FILE_TYPE;
        // check if image
        $fileinfo = finfo_open(FILEINFO_MIME_TYPE);
        // file upload will store in tmp_name
        $file_mime_type = finfo_file($fileinfo, $_FILES['file']['tmp_name']);
        if (!in_array($file_mime_type, $mime_types)) {
            return Message::message(false, 'Invalid file type, file must be an image');
        }

        // standardize image before upload to server
        $pathinfo = pathinfo($_FILES['file']['name']);
        $filename = $pathinfo['filename'];
        $filename = preg_replace('/[^a-zA-Z0-9_-]/', '_', $filename);

        // Handle override file exist in uploads folder
        $fullname = $filename . '.' . $pathinfo['extension'];
        // create path to uploads folder in server
        $fileToHost = '../../uploads/' . $fullname;
        $i = 1;
        while (file_exists($fileToHost)) {
            $fullname = $filename . "-$i." . $pathinfo['extension'];
            $fileToHost = '../../uploads/' . $fullname;
            $i++;
        }

        $fileTmp = $_FILES['file']['tmp_name'];
        if (move_uploaded_file($fileTmp, $fileToHost)) {
            return Message::message(true, $fullname);
        } else {
            return Message::message(false, "Error uploading!");
        }
    } catch (Exception $e) {
        return Message::message(false, $e->getMessage());
    }
}
?>

<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if(!isset($_conn))
        $conn = require_once dirname(dirname(__DIR__)) . "/inc/db.php";

    $fullname = upload_file()['message'];
    $userId = $_SESSION['userId'];
    $imageUrl = APP_URL . "/uploads/" . $fullname;

    $status = User::updateUser($conn, $userId, ['imageUrl' => $imageUrl]);

    if($status['status']) $_SESSION['image'] = $imageUrl;

    throwStatusMessage($status);
}
?>