<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

//Load Composer's autoloader
require dirname(dirname(__DIR__)) . '/vendor/mailer/src/Exception.php';
require dirname(dirname(__DIR__)) . '/vendor/mailer/src/PHPMailer.php';
require dirname(dirname(__DIR__)) . '/vendor/mailer/src/SMTP.php';

require_once dirname(__DIR__) . "/services/message.php";
require_once dirname(dirname(__DIR__)) . "/config.php";
class Mail
{
    public static function sendEmail($email, $username, $subject, $body)
    {
        try {
            //Create an instance; passing `true` enables exceptions
            $mail = new PHPMailer(true);

            // $mail->SMTPDebug = SMTP::DEBUG_SERVER;
            $mail->isSMTP();
            $mail->Host       = MAIL_HOST;                   
            $mail->SMTPAuth   = true;
            // $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;  
            $mail->SMTPSecure  = MAIL_SMTP_SECURE;
            $mail->Username   = MAIL_USER;
            $mail->Password   = MAIL_PASSWORD;
            $mail->Priority    = 1; // Highest priority - Email priority (1 = High, 3 = Normal, 5 = low)          //Enable implicit TLS encryption
            $mail->Port       = MAIL_PORT;
            $mail->CharSet     = 'UTF-8';
            $mail->Encoding    = '8bit';
            $mail->Subject     = 'Email Using Gmail';
            $mail->ContentType = 'text/html; charset=utf-8\r\n';
            //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

            //Recipients
            $mail->setFrom(MAIL_USER, 'SE Mailer');
            $mail->addAddress($email, $username);     //Add a recipient
            $mail->addReplyTo(MAIL_USER, 'SE Information');

            //Attachments
            // $mail->addAttachment('/var/tmp/file.tar.gz');         //Add attachments
            // $mail->addAttachment('/tmp/image.jpg', 'new.jpg');    //Optional name
            // $otp = rand(100000, 999999);
            // Create opt outside
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body = $body;

            if (!$mail->send())
                return Message::message(false, "Cannot not send an email for this address");
            return Message::message(true, "Email sent successfully");
        } catch (Exception $e) {
            return Message::message(false, "Message could not be sent. Mailer Error: {$mail->ErrorInfo}");
        }
    }
}
