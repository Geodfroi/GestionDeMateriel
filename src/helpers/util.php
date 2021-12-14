<?php

declare(strict_types=1);

################################
## Joël Piguet - 2021.12.14 ###
##############################

namespace app\helpers;

use app\constants\Alert;
use app\constants\AlertType;
use app\constants\LogInfo;
use app\constants\Settings;
use app\helpers\Logging;
use app\models\User;
use app\routes\BaseRoute;

use DateTime;

/**
 * Utility class containing useful static functions.
 */
class Util
{
    /**
     * Encrypt password in plain text into a 30 caracters encrypted hashed string.
     * 
     * @param string $password_plain Password in plain text.
     * @return string Encrypted string password.
     */
    public static function encryptPassword(string $password_plain): string
    {
        return password_hash($password_plain, PASSWORD_BCRYPT);
    }

    /**
     * Get number of days until date.
     * 
     * @param DateTime $date.
     * @return int Return number of days>; 0 if date is already past.
     */
    public static function getDaysUntil(DateTime $date): int
    {
        $today = Util::stripTimeComponent(new DateTime());
        $date = Util::stripTimeComponent($date);

        $interval = $today->diff($date);
        $days = $interval->days;
        if ($interval->invert) {
            $days = -$days;
        }
        return $days;
    }

    /**
     * Generate a valid random password.
     */
    public static function getRandomPassword()
    {
        $password_candidate = Util::randomString(Settings::DEFAULT_PASSWORD_LENGTH);
        $has_number = preg_match('@[0-9]@', $password_candidate);
        $has_letters = preg_match('@[a-zA-Z]@', $password_candidate);
        if ($has_number && $has_letters) {
            return $password_candidate;
        }
        return Util::getRandomPassword();
    }

    /**
     * Get a randomly generated string of set lenght.
     * https://www.w3docs.com/snippets/php/how-to-generate-a-random-string-with-php.html
     * 
     * @param int $lenght String lenght.
     * @return string String of set lenght.
     */
    public static function randomString(int $lenght): string
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $randomString = '';

        for ($i = 0; $i < $lenght; $i++) {
            $index = rand(0, strlen($characters) - 1);
            $randomString .= $characters[$index];
        }

        return $randomString;
    }

    /**
     * Read text file content and replace keywords with their value.
     * 
     * @param string $file_path File path.
     * @param array $params Associative array of parameters to be inserted in text content.
     * @return string File content edited with parameters.
     */
    public static function readFile(string $file_path, array $params): string
    {
        $content = file_get_contents($file_path . '.txt');
        foreach ($params as $key => $value) {

            Logging::debug('key: ' . sprintf('$%s', $key));
            if (is_string($value)) {
                $content = str_replace(sprintf('$%s', $key), $value, $content);
            }
        }
        return $content;
    }

    /**
     * Load a php template in memory and returns a content string.
     *
     * @param string $name The name of the template.
     * @param array $data The variables to be used in php templates.
     * @param string $folder_path Template directory.
     * @return string Rendered template as string.
     */
    public static function renderTemplate(string $name, array $data = [], string $folder_path): string
    {
        // extract array variables into the local scope so they can be to be used in the template scripts.
        extract($data, EXTR_OVERWRITE);
        // start buffering the string;
        ob_start();
        // load file content at path and resolve php script to a string in the buffer;
        require  $folder_path . DIRECTORY_SEPARATOR . $name . '.php';
        // flush the buffer content to the variable
        $rendered = ob_get_clean();

        return (string)$rendered;
    }

    /**
     * Create a new password, send a renewal email and modify database record. Includes logging.
     * 
     * @param BaseRoute Route receiving the alerts.
     * @param User User.
     */
    public static function renewPassword(BaseRoute $route, User $user)
    {
        $former_password = $user->getPassword();

        $plain_password = Util::getRandomPassword();
        $encrypted = Util::encryptPassword($plain_password);

        if (Database::users()->updatePassword($user->getId(), $encrypted)) {

            if (Mailing::passwordChangeNotification($user,  $plain_password)) {
                $route->showAlert(AlertType::SUCCESS, sprintf(Alert::NEW_PASSWORD_SUCCESS, $user->getLoginEmail()));

                Logging::info(LogInfo::NEW_PASSWORD_ISSUED, [
                    'user-id' => $user->getId(),
                    'login' => $user->getLoginEmail()
                ]);

                return;
            }
            // attempt to roll back update.
            Database::users()->updatePassword($user->getId(), $former_password);
        }
        $route->showAlert(AlertType::FAILURE, Alert::NEW_PASSWORD_FAILURE);
    }

    /**
     * Cut string in two at last occurence of separator character.
     * https://stackoverflow.com/questions/67025055/explode-string-on-the-last-occurrence-of-character
     * 
     * @param string @separator Separator character.
     * @param string @str String to cut.
     * @return array Array of string or empty array if separator character was not found.
     */
    public static function separateAtLast(string $separator, string $str): array
    {
        $last_pos = strrpos($str, $separator);
        if ($last_pos) {
            $prefix = substr($str, 0, $last_pos);
            $suffix = substr($str, $last_pos + 1);
            return [$prefix, $suffix];
        }
        return [];
    }

    /**
     * str_contains was introduced in php8
     * https://stackoverflow.com/questions/66519169/call-to-undefined-function-str-contains-php
     * 
     * @param string $haystack;
     * @param string $needle;
     * @return bool True if found or $needle was empty string.
     */
    public static function str_contains(string $haystack, string $needle): bool
    {
        return '' === $needle || false !== strpos($haystack, $needle);
    }

    /**
     * Strip time component from a DateTime value. Useful for date comparaison.
     * 
     * @param DateTime Date value.
     * @return DateTime Striped Date value.
     */
    public static function stripTimeComponent(DateTime $date): DateTime
    {
        return DateTime::createFromFormat('Y-m-d', $date->format('Y-m-d'));
    }
}
