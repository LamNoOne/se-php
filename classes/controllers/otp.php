<?php

require_once dirname(__DIR__) . "/services/message.php";
require_once dirname(dirname(__DIR__)) . "/config.php";
class OTP
{
    public $id;
    public $userId;
    public $otpCode;
    public $otpStatus;
    public $createdAt;

    public function __construct($userId = null, $otpCode = null, $otpStatus = 1)
    {
        $this->userId = $userId;
        $this->otpCode = $otpCode;
        $this->otpStatus = $otpStatus;
    }
    public static function getOTP($conn, $otpId)
    {
        try {
            $query = "SELECT * FROM otp WHERE id = :otpId";
            $stmt = $conn->prepare($query);
            $stmt->bindValue(":otpId", $otpId, PDO::PARAM_INT);
            $stmt->setFetchMode(PDO::FETCH_INTO, new OTP());
            if (!$stmt->execute()) {
                throw new Exception('Can not execute query');
            }
            return $stmt->fetch();
        } catch (Exception $e) {
            return Message::message(false, $e->getMessage());
        }
    }

    public static function getLatestOTPByUserId($conn, $userId) {
        try {
            $query = "SELECT * FROM otp WHERE userId = :userId ORDER BY id DESC LIMIT 1";
            $stmt = $conn->prepare($query);
            $stmt->bindValue(":userId", $userId, PDO::PARAM_INT);
            $stmt->setFetchMode(PDO::FETCH_INTO, new OTP());
            if (!$stmt->execute()) {
                throw new Exception('Can not execute query');
            }
            return $stmt->fetch();
        } catch (Exception $e) {
            return Message::message(false, $e->getMessage());
        }
    }

    public static function disableOtp($conn, $otpId)
    {
        try {
            $query = "UPDATE otp SET otpStatus = 0 WHERE id = :otpId";
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':otpId', $otpId);
            if (!$stmt->execute()) {
                throw new Exception('Can not execute query');
            }
            return Message::message(true, "OTP status updated successfully");
        } catch (Exception $e) {
            return Message::message(false, $e->getMessage());
        }
    }

    public static function verifyOTP($conn, $otpId, $otpCodeOutside)
    {
        $timezone = new DateTimeZone(TZ_DEFAULT);
        $otpCodeData = static::getOTP($conn, $otpId);

        if (empty($otpCodeData)) return false;
        if (!$otpCodeData->otpStatus) return false;

        $otpCode = $otpCodeData->otpCode;
        $createdTime = $otpCodeData->createdAt;

        // Create a DateTime object for the provided date and set the timezone
        $date = new DateTime($createdTime, $timezone);

        // Get the current date and time in Vietnam timezone
        $now = new DateTime('now', $timezone);

        // Calculate the difference between the two dates
        $difference = abs($now->getTimestamp() - $date->getTimestamp());

        // Check otp code equals and in time
        return strval($otpCode) === strval($otpCodeOutside) && $difference < OTP_EXPIRED_TIME;
    }

    public static function generateOTP()
    {
        $otp = rand(100000, 999999);
        return $otp;
    }

    public function createOTP($conn)
    {
        try {
            $createOTPStatement =
                "INSERT INTO otp (userId, otpCode, otpStatus) VALUES (:userId, :otpCode, :otpStatus)";

            $stmt = $conn->prepare($createOTPStatement);
            $stmt->bindParam(':userId', $this->userId);
            $stmt->bindParam(':otpCode', $this->otpCode);
            $stmt->bindParam(':otpStatus', $this->otpStatus);
            if (!$stmt->execute())
                return Message::message(false, "Could not create OTP");
            $otpId = $conn->lastInsertId();
            return Message::messageData(true, "OTP created successfully", ['otpId' => $otpId]);
        } catch (Exception $e) {
            return Message::message(false, $e->getMessage());
        }
    }
}
