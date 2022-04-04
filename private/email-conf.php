<?php
# Code so that emailing will work
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require "phpmailer/src/Exception.php";
require "phpmailer/src/PHPMailer.php";
require "phpmailer/src/SMTP.php";

# Mailing function
function mailing($recp,$recp_name,$body,$alt_body){
    # P
    include("../help.php");
    # Mail instance
    $mail = new PHPMailer(true);
    # Settings
    try {
        # Server settings
        $mail->isSMTP();
        $mail->Host = "smtp.ionos.co.uk";
        $mail->SMTPAuth = true;
        $mail->Username = "info@pro-fect.com"; 
        $mail->Password = $launcher_key; 
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        # Recipient
        $mail->setFrom("info@pro-fect.com", "Pro-fect");
        $mail->addAddress($recp, $recp_name);
        $mail->addReplyTo("info@pro-fect.com", "Pro-fect");

        # Content
        $mail->isHTML(true);
        $mail->Subject = "Verification";
        $mail->Body = $body;
        $mail->AltBody = $alt_body;

        # Send email
        $mail->send();
    } catch (Exception $e) {
        $msg = "The email could not be sent.\n Mailer error {$mail->ErrorInfo}";
    }
};