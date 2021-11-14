<?php

declare(strict_types=1);

################################
## Joël Piguet - 2021.11.14 ###
##############################

namespace helpers;

/**
 * static functions connected to emails bundled together in a class
 */
class Mailing
{
    /**
     * Send an email change notification to the user.
     * 
     * @param string $to User email.
     * @param string $password New password in plain text.
     */
    public static function passwordChangeNotification($to, $password)
    {
        $subject = 'Notification HEdS : ' . APP_NAME;
        $message = "Votre mot de passe pour l'application " . APP_NAME . ' a été changé avec succès' . PHP_EOL . 'Votre nouveau mot de passe est: ' . $password . PHP_EOL;
        $headers = 'From: ' . ADMIN_EMAIL;

        mail($to, $subject, $message, $headers);
    }
}
