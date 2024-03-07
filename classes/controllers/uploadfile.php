<?php
require_once dirname(dirname(__DIR__)) .  "/inc/utils.php";

class UploadFile
{
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
    public static function process($fieldName)
    {
        try {
            if (empty($_FILES)) {
                return Message::message(false, 'Cannot upload files');
            }

            $rs = Errorfileupload::err($_FILES[$fieldName]['error']);
            if ($rs != 'OK') {
                return Message::message(false, $rs);
            }

            $fileMaxSize = FILE_MAX_SIZE;
            if ($_FILES[$fieldName]['size'] > $fileMaxSize) {
                return Message::message(false, 'File too large, must smaller than: ' .  $fileMaxSize);
            }

            // limit file image type
            $mime_types = FILE_TYPE;
            // check if image
            $fileinfo = finfo_open(FILEINFO_MIME_TYPE);
            // file upload will store in tmp_name
            $file_mime_type = finfo_file($fileinfo, $_FILES[$fieldName]['tmp_name']);
            if (!in_array($file_mime_type, $mime_types)) {
                return Message::message(false, 'Invalid file type, file must be: ' . implode('; ', FILE_TYPE));
            }

            // standardize image before upload to server
            $pathinfo = pathinfo($_FILES[$fieldName]['name']);
            $filename = $pathinfo['filename'];
            $filename = preg_replace('/[^a-zA-Z0-9_-]/', '_', $filename);

            // create path to uploads folder in server
            $path = '/uploads/';
            $uploadFolder = dirname(dirname(__DIR__)) . $path;
            $fullName = $filename . '.' . $pathinfo['extension'];
            $fileToHost = $uploadFolder . $fullName;
            $uploadedFileUrl = APP_URL . $path . $fullName;

            // Handle override file exist in uploads folder
            $i = 1;
            while (file_exists($fileToHost)) {
                $fullName = $filename . "-$i." . $pathinfo['extension'];
                $fileToHost = $uploadFolder . $fullName;
                $uploadedFileUrl = APP_URL . $path . $fullName;
                $i++;
            }

            $fileTmp = $_FILES[$fieldName]['tmp_name'];

            if (move_uploaded_file($fileTmp, $fileToHost)) {
                return [
                    ...Message::message(true, 'Upload file successfully'),
                    'url' => $uploadedFileUrl
                ];
            }
            return Message::message(false, 'Upload file failed');
        } catch (Exception $e) {
            return Message::message(false, $e->getMessage());
        }
    }
}