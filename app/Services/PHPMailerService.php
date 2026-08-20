<?php

namespace App\Services;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class PHPMailerService
{
    protected $mail;

    public function __construct()
    {
        $this->mail = new PHPMailer(true);

        // $this->mail->isSMTP();
        // $this->mail->Host       = 'email-smtp.ap-south-1.amazonaws.com'; // SES endpoint
        // $this->mail->SMTPAuth   = true;
        // $this->mail->Username   = 'AKIAZXNTWQL6FN6WO2GY'; // SES SMTP username
        // $this->mail->Password   = 'BOwD7/vnPulG2JaD6Sr4MKORhLByDzbhzmqnE3flDCV+'; // SES SMTP password
        // $this->mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        // $this->mail->Port       = 587;

        $this->mail->isSMTP();
        $this->mail->Host = 'smtp.gmail.com';
        $this->mail->SMTPAuth = true;
        $this->mail->Username = 'dgacams@gmail.com';
        $this->mail->Password = 'zgmm nyqb annu ohfu';
        $this->mail->SMTPSecure = 'ssl';
        $this->mail->Port = 465;

        // Optional: disable SSL verification (not recommended for production)
        $this->mail->SMTPOptions = [
            'ssl' => [
                'verify_peer'       => false,
                'verify_peer_name'  => false,
                'allow_self_signed' => true,
            ],
        ];
    }

    public function sendEmail($to, $subject, $body, $cc = [])
    {
        try {

            $this->mail->clearAddresses();
            $this->mail->clearCCs();
            $this->mail->clearBCCs();
            $this->mail->clearReplyTos();
            $this->mail->clearAttachments();
            // Sender
            $this->mail->setFrom('noreply@auditcams.in', 'CAMS');
            $this->mail->addAddress($to);

            // CC support
            if (!empty($cc) && is_array($cc)) {
                foreach ($cc as $ccEmail) {
                    if (!empty($ccEmail)) {
                        $this->mail->addCC($ccEmail);
                    }
                }
            }

            // Email content
            $this->mail->isHTML(true);
            $this->mail->Subject = $subject;
            $this->mail->Body    = $body;

            $this->mail->send();
            return 'Message has been sent';
        } catch (Exception $e) {
            return "Message could not be sent. Mailer Error: {$this->mail->ErrorInfo}";
        }
    }
}
