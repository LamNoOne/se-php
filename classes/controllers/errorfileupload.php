<?php
class ErrorFileUpload
{
    public static function err($code)
    {
        switch ($code) {
            case UPLOAD_ERR_OK:
                $message = Message::message(UPLOAD_ERR_OK, 'OK');
                break;
            case UPLOAD_ERR_INI_SIZE:
                $message = Message::message(UPLOAD_ERR_INI_SIZE, 'The uploaded file exceeds the upload_max_filesize directive in php.ini');
                break;
            case UPLOAD_ERR_FORM_SIZE:
                $message = Message::message(UPLOAD_ERR_FORM_SIZE, 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form');
                break;
            case UPLOAD_ERR_PARTIAL:
                $message = Message::message(UPLOAD_ERR_PARTIAL, 'The uploaded file was only partially uploaded');
                break;
            case UPLOAD_ERR_NO_FILE:
                $message = Message::message(UPLOAD_ERR_NO_FILE, 'No file was uploaded');
                break;
            case UPLOAD_ERR_NO_TMP_DIR:
                $message = Message::message(UPLOAD_ERR_NO_TMP_DIR, 'Missing a temporary folder');
                break;
            case UPLOAD_ERR_CANT_WRITE:
                $message = Message::message(UPLOAD_ERR_CANT_WRITE, 'Failed to write file to disk');
                break;
            case UPLOAD_ERR_EXTENSION:
                $message = Message::message(UPLOAD_ERR_EXTENSION, 'File upload stopped by extension');
                break;
            default:
                $message = Message::message(-1, 'Unknown upload error');
                break;
        }
        return $message;
    }
}
