<?php

namespace App\Http\Traits;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

trait Mailer
{
    /**
     * Send an email with the provided body, subject, and recipient.
     *
     * @param  string  $body      The HTML body content of the email.
     * @param  string  $subject   The subject line of the email.
     * @param  string  $recipient The email address of the recipient.
     * @return bool True if the email was sent successfully; otherwise, false.
     */
    public function sendMail($body, $subject, $recipient)
    {
        $mail = new PHPMailer(true);

        try {
            $this->configureMailSettings($mail);
            $this->setRecipient($mail, $recipient);

            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body    = $body;
            $mail->AltBody = '';

            $mail->send();
            info('Message has been sent');

            return true;
        } catch (Exception $e) {
            info($mail->ErrorInfo);
            info($e->getMessage());

            return false;
        }
    }

    /**
     * Configure the mail settings for PHPMailer.
     *
     * @param  PHPMailer  $mail The PHPMailer instance to configure.
     * @return PHPMailer The configured PHPMailer instance.
     */
    public function configureMailSettings($mail)
    {
        $mail->SMTPDebug = SMTP::DEBUG_SERVER;
        $mail->isSMTP();
        $mail->Host       = env('MAIL_HOST');
        $mail->SMTPAuth   = true;
        $mail->Username   = env('MAIL_USERNAME');
        $mail->Password   = env('MAIL_PASSWORD');
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = env('MAIL_PORT');

        return $mail;
    }

    /**
     * Set the recipient of the email.
     *
     * @param  PHPMailer  $mail      The PHPMailer instance.
     * @param  string     $recipient The email address of the recipient.
     * @return void
     */
    public function setRecipient($mail, $recipient)
    {
        $mail->setFrom(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'));
        $mail->addAddress($recipient);
    }
}
