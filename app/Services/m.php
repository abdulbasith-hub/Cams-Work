<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '/data/html/cams/vendor/phpmailer/phpmailer/src/PHPMailer.php';
require '/data/html/cams/vendor/phpmailer/phpmailer/src/SMTP.php';
require '/data/html/cams/vendor/phpmailer/phpmailer/src/Exception.php';

$mail = new PHPMailer(true);
try {
    // Server settings
    $mail->isSMTP();                                            // Set mailer to use SMTP
    $mail->Host       = '10.236.242.79';                     // Specify main and backup SMTP servers
    $mail->SMTPAuth   = true;                                   // Enable SMTP authentication
    $mail->Username   = 'cams.dga';                        // SMTP username
    $mail->Password   = 'kwic>o#7Fu@g';                        // SMTP password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            // Enable implicit TLS encryption
    $mail->Port       = 465;                                    // TCP port to connect to; use 587 if you set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

    // *** OPTIONAL: Disable peer verification (use with caution) ***
    $mail->SMTPOptions = array(
        'ssl' => array(
            'verify_peer' => false,
            'verify_peer_name' => false,
            'allow_self_signed' => true
        )
    );

    // Recipients
    $mail->setFrom('cams.dga@tn.gov.in', 'Mailer');
    $mail->addAddress('nic.srama@gmail.com', 'Recipient Name');     // Add a recipient

    // Content
    $mail->isHTML(true);                                       // Set email format to HTML
    $mail->Subject = 'Here is the subject';
    $mail->Body    = 'This is the HTML message body <b>in bold!</b>';
    $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

    $mail->send();
    echo 'Message has been sent';
} catch (Exception $e) {
    echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
}
	
    
