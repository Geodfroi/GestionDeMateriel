<?php

declare(strict_types=1);

################################
## Joël Piguet - 2021.12.05 ###
##############################

namespace app\helpers;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

use app\helpers\Util;

/**
 * static functions connected to emails bundled together in a class
 *
 */
class Mailing
{
    /**
     * Return html page to be inserted inside an email.
     * 
     * @param string $email_template Name of email template.
     * @param array $params Parameters to be inserted inside html template.
     * @return string html page as string.
     */
    private static function formatEmail(string $email_tamplate, array $params): string
    {
        $content = Util::renderTemplate('', $params, EMAIL_TEMPLATES_PATH);
        return Util::renderTemplate('email_base', ['content' => $content], EMAIL_TEMPLATES_PATH);
    }

    /**
     * Send a password change notification to the user mail box.
     * 
     * @param string $sender User alias or email address.
     * @param array $emails Message will be sent to those addresses.
     * @param string $password New password in plain text.
     * @return bool True if emails are sent correctly.
     */
    public static function passwordChangeNotification(string $sender, array $emails, string $password): bool
    {
        $params = [
            'username' => $sender,
            'app_name' => APP_NAME,
            'password' => $password
        ];
        $html =  Mailing::formatEmail('newpassword', $params);

        return Mailing::send($emails, EMAIL_SUBJECT_NEW_PASSWORD,  $html, '');
    }



    /**
     * Send an html formatted email using a gmail address.
     * https://phppot.com/php/send-email-in-php-using-gmail-smtp/
     * 
     * @param array $recipients Recipients email addresses.
     * @param string $subject Email subject.
     * @param string $html_content HTML formatted content.
     * @param string $plain_content Plain content for client refusing html version.
     * @return true True if email is sent properly.
     */
    private static function send(array $recipients, string $subject, string $html_content, string $plain_content): bool
    {
        $mail = new PHPMailer(true);

        try {
            // Server settings
            $mail->SMTPDebug = SMTP::DEBUG_SERVER; // for detailed debug output
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            $mail->Username = APP_EMAIL;
            $mail->Password = APP_EMAIL_PASSWORD;

            // Sender and recipient settings
            $mail->setFrom(APP_EMAIL, APP_EMAIL_SENDER);
            foreach ($recipients as $recipient) {
                $mail->addAddress($recipient);
            }
            // $mail->addReplyTo('innov.heds@gmail.com', 'Sender Name'); // allow answer back

            // Setting the email content
            $mail->IsHTML(true);
            $mail->Subject = $subject;
            $mail->Body = $html_content;
            $mail->AltBody = $plain_content;

            $mail->send();
            return true;
        } catch (Exception) {
            error_log("Error in sending email. Mailer Error: {$mail->ErrorInfo}");
            return false;
        }
    }
}
