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
     * - 'status': A code that indicating the file upload status (0 - UPLOAD_ERR_OK: upload file successfully).
     * - 'message': A string describing the upload result.
     * - 'url': (Optional) The URL of the uploaded file, available if upload is successful.
     */
    public static function process($fieldName)
    {
        try {
            if (empty($_FILES)) {
                return [
                    'status' => UPLOAD_ERR_NO_FILE,
                    'message' => 'No files was uploaded'
                ];
            }

            $rs = Errorfileupload::err($_FILES[$fieldName]['error']);
            if ($rs['status'] != UPLOAD_ERR_OK) {
                return [
                    'status' => $rs['status'],
                    'message' => $rs['message']
                ];
            }

            $fileMaxSize = FILE_MAX_SIZE;
            if ($_FILES[$fieldName]['size'] > $fileMaxSize) {
                return [
                    'status' => UPLOAD_ERR_FORM_SIZE,
                    'message' => 'File too large, must smaller than: ' .  $fileMaxSize
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
                    'status' => UPLOAD_ERR_EXTENSION,
                    'message' => 'Invalid file type, file must be: ' . implode('; ', FILE_TYPE)
                ];
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
                    'status' => UPLOAD_ERR_OK,
                    'message' => 'Upload file successfully',
                    'url' => $uploadedFileUrl
                ];
            }
            return [
                'status' => UPLOAD_ERR_CANT_WRITE,
                'message' => 'Upload file failed'
            ];
        } catch (Exception $e) {
            return [
                'status' => UPLOAD_ERR_CANT_WRITE,
                'message' => $e->getMessage()
            ];
        }
    }
}
