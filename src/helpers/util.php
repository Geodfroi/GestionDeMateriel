<?php

declare(strict_types=1);

################################
## Joël Piguet - 2022.03.10 ###
##############################

namespace app\helpers;

use DateTime;
use DirectoryIterator;

use app\constants\LogInfo;
use app\constants\Session;
use app\constants\Settings;
use app\helpers\Logging;
use app\models\User;

/**
 * Utility class containing useful static functions.
 */
class Util
{
    /**
     * Display a popup alert message recovered from SESSION storage.
     */
    public static function displayAlert()
    {
        if (!isset($_SESSION[SESSION::ALERT])) {
            return [];
        }

        $alert_array = $_SESSION[SESSION::ALERT];
        Logging::debug('alert_array', $alert_array);
        //check if it is the correct page to display stored alert.
        if ($alert_array[2] != $_SESSION['route']) {
            return [];
        }

        unset($_SESSION[SESSION::ALERT]);

        if (DEBUG_MODE) {
            Logging::debug('alert', $alert_array);
        }
        return [
            'type' => $alert_array[0],
            'msg' => $alert_array[1],
            'timer' => Settings::ALERT_TIMER,
        ];
    }

    /**
     * Erase old files from the specified folder, starting from the oldest, when they are appened with "_YYYYmmdd" date suffix. Used to remove old logs or old db backups.
     * 
     * @param $folder
     * @param $file_name File original name.
     * @param $file_ext File extension without dot.
     * @param int $max_files Maximum # of files left in folder. 
     */
    public static function eraseOldFiles(string $folder, string $file_root, string $file_ext, int $max_count)
    {
        $dates = [];

        // find files in folder and extract their date component.
        foreach (new DirectoryIterator($folder) as $file) {
            if (!$file->isDot()) {
                $file_name = $file->getFilename();

                // check if file name match the pattern. e.g. find 'backup_20220101.db' with regex
                $pattern = "/^" . $file_root . "_\d{8}\." . $file_ext . "$/";
                if (preg_match($pattern, $file_name)) {
                    // recover date from file name; e.g. recover '20211215' string from 'backup_20211215.db'
                    $str_date = substr($file_name, strlen($file_root) + 1, 8);
                    array_push($dates, $str_date);
                }
            }
        }

        // sort date array in reverse alphabetical order => oldest date will be last in array.
        rsort($dates);

        // cull oldest files when the number of file exceeds $max_files limit.
        for ($n = $max_count; $n < count($dates); $n++) {
            $date = $dates[$n];
            $file_path = $folder . DIRECTORY_SEPARATOR . $file_root . '_' . $date . '.' . $file_ext;
            unlink($file_path);
        }
    }

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
        $password_candidate = Util::randomString(Settings::USER_PASSWORD_DEFAULT_LENGTH);
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
    public static function readFile(string $file_path, array $params = []): string
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
     * Send a header to the browser requesting a redirection to the path provided. Optionaly display an alert after redirection.
     * 
     * @param string $uri The redirection path. Use the constants in Routes class to avoir typing mistakes.
     * @param string $alert_type Optional alert type. Use AlertType const.
     * @param string $alert_msg Optional alert message to be displayed after redirection.
     * @return string Return empty string.
     */
    public static function requestRedirect(string $uri, string $alert_type = "", string $alert_msg = ""): string
    {
        if (strlen($alert_type) != 0 && strlen($alert_msg) != 0) {
            Util::storeAlert($uri, $alert_type, $alert_msg);
        }
        //The header php function will send a header message to the browser, here signaling for redirection.
        header("Location: $uri", true);
        return "";
    }

    /**
     * Store alert content to be fetched in next page display.
     * 
     * @param string $msg Alert message.
     * @param string $type Alert type. Use AlertType const.
     * @param string $display_page Page on which to display the alert.
     */
    public static function storeAlert(string $display_page, string $type, string $msg)
    {
        $_SESSION[SESSION::ALERT] = [$type, $msg, $display_page];
    }

    /**
     * https://stackoverflow.com/questions/834303/startswith-and-endswith-functions-in-php
     * 
     * @param string $haystack;
     * @param string $needle;
     * @return bool True if $haystack start with $needle.
     */
    public static function startsWith(string $haystack, string $needle): bool
    {
        $length = strlen($needle);
        return substr($haystack, 0, $length) === $needle;
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
     * @param User User.
     * @return bool True if successful.
     */
    public static function renewPassword(User $user): bool
    {
        $former_password = $user->getPassword();

        $plain_password = Util::getRandomPassword();
        $encrypted = Util::encryptPassword($plain_password);

        if (Database::users()->updatePassword($user->getId(), $encrypted)) {

            if (Mailing::passwordChangeNotification($user,  $plain_password)) {

                Logging::info(LogInfo::NEW_PASSWORD_ISSUED, [
                    'user-id' => $user->getId(),
                    'login' => $user->getLoginEmail()
                ]);

                return true;
            }
            // attempt to roll back update.
            Database::users()->updatePassword($user->getId(), $former_password);
        }
        return false;
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

    // /**
    //  * Turn an associative array into a string.
    //  */
    // public static function associativeToString(array $array): string
    // {
    //     $str = "";
    //     foreach ($array as $key => $val) {
    //         $str = $str . strval($key) . '=' . strval($val) . ';';
    //     }
    //     return $str;
    // }