<?php

declare(strict_types=1);

################################
## Joël Piguet - 2021.12.20 ###
##############################

namespace app\helpers;

use PHPMailer\PHPMailer\PHPMailer;
// use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

use app\constants\AppPaths;
use app\constants\Mail;
use app\constants\P_Settings;
use app\constants\Settings;
use app\helpers\Logging;
use app\helpers\Util;
use app\models\User;

/**
 * static functions connected to emails bundled together in a class
 */
class Mailing
{
    /**
     * Return html page to be inserted inside an email.
     * 
     * @param string $email_template Name of email template.
     * @param array $params Associative array of parameters to be inserted in template.
     * @return array String array containing Html and unformatted content.
     */
    private static function formatBody(string $email_template, array $params): array
    {
        Logging::debug('params', $params);

        $html_content = Util::renderTemplate($email_template, $params, AppPaths::EMAIL_TEMPLATES_PATH);
        $plain_content = Util::readFile(AppPaths::EMAIL_TEMPLATES_PATH . DIRECTORY_SEPARATOR . $email_template, $params);

        return [
            Util::renderTemplate('email_base', ['content' => $html_content], AppPaths::EMAIL_TEMPLATES_PATH),
            $plain_content,
        ];
    }

    /**
     * Send a password change notification to the user mail box.
     * 
     * @param User $user Email recipient.
     * @param string $password New password in plain text.
     * @return bool True if emails are sent correctly.
     */
    public static function passwordChangeNotification(User $user, string $password): bool
    {
        [$html, $plain_text] = Mailing::passwordChangeNotificationBody($user->getAlias(), $password);
        return Mailing::send($user->getMailingAddresses(), Mail::SUBJECT_NEW_PASSWORD,  $html, $plain_text);
    }

    /**
     * Return formatted body for password change.
     * 
     * @param string $recipient User alias or email address.
     * @param string $password New password in plain text.
     * @return array String array containing Html and unformatted content.
     */
    public static function passwordChangeNotificationBody(string $recipient, string $password): array
    {
        return Mailing::formatBody('newpassword', [
            'app_name' => Settings::APP_NAME,
            'url' => Settings::APP_FULL_URL,
            'alias' => $recipient,
            'password' => $password,
        ]);
    }

    /**
     * Send an email to user to remind them of soon to be expired items.
     * 
     * @param User $user Email recipient.
     * @param array $reminders Associative array containing 'article' and 'delay' keys.
     * @return bool True if emails are sent correctly.
     */
    public static function peremptionNotification(User $user, array $reminders): bool
    {
        Logging::info("Sending peremption email to {$user->getAlias()}", $reminders);
        [$html, $plain_text] = Mailing::peremptionNotificationBody($user->getAlias(), $reminders);
        return Mailing::send($user->getMailingAddresses(), Mail::SUBJECT_PEREMPTION_REMINDER,  $html, $plain_text);
    }

    /**
     * Return formatted body for peremption notification.
     * 
     * @param string $recipient User alias or email address.
     * @param array $reminders Associative array containing 'article' and 'delay' keys.
     * @return array String array containing Html and unformatted content.
     */
    public static function peremptionNotificationBody(string $recipient, array $reminders): array
    {
        return Mailing::formatBody('reminder', [
            'app_name' => Settings::APP_NAME,
            'url' => Settings::APP_FULL_URL,
            'alias' => $recipient,
            'reminders' => $reminders,
        ]);
    }

    /**
     * Send an invite email to new user.
     * 
     * @param string $password_plain Newly generated password in plain text.
     * @return bool True if emails are sent correctly.
     */
    public static function userInviteNotification(User $user, string $password_plain): bool
    {
        Logging::info("Sending user invite email to {$user->getAlias()}");
        [$html, $plain_text] = Mailing::userInviteNotificationBody($user->getLoginEmail(), $password_plain);
        return Mailing::send($user->getMailingAddresses(), Mail::SUBJECT_USER_INVITE,  $html, $plain_text);
    }

    /**
     * Return formatted body for user invite notification.
     * 
     * @param string $login New user email as login.
     * @param string $password_plain Newly generated password in plain text.
     * @return array String array containing Html and unformatted content.
     */
    public static function userInviteNotificationBody(string $login, string $password_plain): array
    {
        return Mailing::formatBody('userinvite', [
            'app_name' => Settings::APP_NAME,
            'url' => Settings::APP_FULL_URL,
            'login' => $login,
            'password' => $password_plain,
        ]);
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
        if (SETTINGS::DEBUG_MODE) {
            $recipients = [SETTINGS::DEBUG_EMAIL];
        }

        $mail = new PHPMailer(true);

        try {
            // Server settings
            // $mail->SMTPDebug = SMTP::DEBUG_SERVER; // for detailed debug output
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            $mail->CharSet = PHPMailer::CHARSET_UTF8;

            $mail->Username = P_Settings::APP_EMAIL;
            $mail->Password = P_Settings::APP_EMAIL_PASSWORD;

            // Sender and recipient settings
            $mail->setFrom(P_Settings::APP_EMAIL, Mail::SENDER);
            foreach ($recipients as $recipient) {
                $mail->addAddress($recipient);
            }
            // $mail->addReplyTo('innov.heds@gmail.com', 'Sender Name'); // allow answer back

            // Setting the email content
            $mail->IsHTML(true);
            $mail->Subject = $subject;
            $mail->Body = $html_content;
            $mail->AltBody = $plain_content;

            if ($mail->send()) {
                return true;
            }
            Logging::error('Failure to send email.', ['error' => $mail->ErrorInfo]);
        } catch (Exception $e) {
            Logging::error('Error in sending email.', ['error' => $e->errorMessage()]);
        }
        return false;
    }
}
