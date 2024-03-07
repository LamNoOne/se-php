<?php
require_once "inc/utils.php";

// Auth::requireLogin();
/**
 * Uploads a file to the server.
 *
 * @param string $fieldName The name of the field containing the file to upload.
 *
 * @return array An associative array containing the upload status and message:
 * - 'status': A boolean indicating the upload status (true for success, false for failure).
 * - 'message': A string describing the upload result.
 * - 'url': (Optional) The URL of the uploaded file, available if upload is successful.
 */
function upload_file($fieldName)
{
    try {
        if (empty($_FILES)) {
            return [
                'status' => false,
                'message' => Message::message(false, 'Can not upload files')
            ];
        }

        $rs = Errorfileupload::err($_FILES[$fieldName]['error']);
        if ($rs != 'OK') {
            return [
                'status' => false,
                'message' => Message::message(false, $rs)
            ];
        }

        $fileMaxSize = FILE_MAX_SIZE;
        if ($_FILES[$fieldName]['size'] > $fileMaxSize) {
            return [
                'status' => false,
                'message' => Message::message(false, 'File too large, must smaller than: ' .  $fileMaxSize)
            ];
        }

        // limit file image type
        $mime_types = FILE_TYPE;
        // check if image
        $fileinfo = finfo_open(FILEINFO_MIME_TYPE);
        // file upload will store in tmp_name
        $file_mime_type = finfo_file($fileinfo, $_FILES[$fieldName]['tmp_name']);
        if (!in_array($file_mime_type, $mime_types)) {
            return [
                'status' => false,
                'message' => Message::message(false, 'Invalid file type, file must be an image')
            ];
        }

        // standardize image before upload to server
        $pathinfo = pathinfo($_FILES[$fieldName]['name']);
        $filename = $pathinfo['filename'];
        $filename = preg_replace('/[^a-zA-Z0-9_-]/', '_', $filename);

        // create path to uploads folder in server
        $fullname = $filename . '.' . $pathinfo['extension'];
        $path = '/uploads/' . $fullname;
        $fileToHost = $_SERVER['DOCUMENT_ROOT'] . $path;
        $uploadedFileUrl = APP_URL . '/' . $path;

        // Handle override file exist in uploads folder
        $i = 1;
        while (file_exists($fileToHost)) {
            $fullname = $filename . "-$i." . $pathinfo['extension'];
            $path = '/uploads/' . $fullname;

            $fileToHost = $_SERVER['DOCUMENT_ROOT'] . $path;
            $uploadedFileUrl = APP_URL . '/' . $path;
            $i++;
        }

        $fileTmp = $_FILES[$fieldName]['tmp_name'];

        if (move_uploaded_file($fileTmp, $fileToHost)) {
            return [
                'status' => true,
                'message' => Message::message(true, $fullname),
                'url' => $uploadedFileUrl
            ];
        }
        return [
            'status' => false,
            'message' => Message::message(false, "Error uploading!"),
        ];
    } catch (Exception $e) {
        return Message::message(false, $e->getMessage());
    }
}
