<?php
declare(strict_types=1);

class Message
{
    public function __construct()
    {
    }

    /**
     * Return message to inform user about event
     * @param boolean $status
     * @param string $message
     */
    public static function message($status = null, string $message = "")
    {
        return [
            "status" => $status,
            "message" => $message
        ];
    }

    /**
     * Return message to inform user about event
     * @param boolean $status
     * @param string $message
     * @param mixed $data
     */
    public static function messageData($status = null, string $message = "", $data = null)
    {
        return [
            "status" => $status,
            "message" => $message,
            "data" => $data
        ];
    }
}
