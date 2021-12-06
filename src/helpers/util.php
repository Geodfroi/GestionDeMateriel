<?php

declare(strict_types=1);

################################
## Joël Piguet - 2021.12.06 ###
##############################

namespace app\helpers;

use app\routes\BaseRoute;

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
     * Generate a valid random password.
     */
    public static function getRandomPassword()
    {
        $password_candidate = Util::randomString(DEFAULT_PASSWORD_LENGTH);
        $has_number = preg_match('@[0-9]@', $password_candidate);
        $has_letters = preg_match('@[a-zA-Z]@', $password_candidate);
        if ($has_number && $has_letters) {
            return $password_candidate;
        }
        return $this->getRandomPassword();
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
     * Load a php template in memory and returns a content string.
     *
     * @param string $name The name of the template.
     * @param array $data The variables to be used in php templates.
     * 
     * @return string Rendered template as string.
     */
    public static function renderTemplate(string $name, array $data = [], string $folder_path): string
    {
        // extract array variables into the local scope so they can be to be used in the template scripts.
        extract($data, EXTR_OVERWRITE);
        // start buffering the string;
        ob_start();
        // load file content at path and resolve php script to a string in the buffer;
        require $folder_path . DIRECTORY_SEPARATOR . $name . '.php';
        // flush the buffer content to the variable
        $rendered = ob_get_clean();

        return (string)$rendered;
    }

    /**
     * Location validation. Location must not be empty and under a set number of caracters.
     * 
     * @param BaseRoute $route Route to forward error messages. 
     * @param string &$location Article's location within the school by reference.
     * @return bool True if validated.
     */
    public static function validateLocation(BaseRoute $route, ?string &$location): bool
    {
        $location = trim($_POST['location']) ?? '';

        if (strlen($location) === 0) {
            $route->setError('location', LOCATION_EMPTY);
            return false;
        }

        $location = ucwords($location);
        if (strlen($location) < LOCATION_MIN_LENGHT) {
            $route->setError('location', sprintf(LOCATION_TOO_SHORT, LOCATION_MIN_LENGHT));
            return false;
        }

        if (strlen($location) > LOCATION_MAX_LENGHT) {
            $route->setError('location', sprintf(LOCATION_TOO_LONG, LOCATION_MAX_LENGHT));
            return false;
        }
        return true;
    }

    /**
     * Validate input and fill $errors array with proper password error text to be displayed if it fails.
     * https://www.codexworld.com/how-to/validate-password-strength-in-php/
     * 
     * @param BaseRoute $route Route to forward error messages.
     * @param string|null $password_candidate Proposed user password by reference.
     * @return bool True if password is properly formatted;
     */
    public static function validatePassword(BaseRoute $route, ?string &$password_candidate): bool
    {
        $password_candidate = trim($_POST['password']) ?? '';
        if ($password_candidate === '') {
            $route->setError('password', PASSWORD_EMPTY);
            return false;
        }
        if (strlen($password_candidate) < USER_PASSWORD_MIN_LENGTH) {
            $route->setError('password', sprintf(PASSWORD_SHORT, USER_PASSWORD_MIN_LENGTH));
            return false;
        }
        $has_number = preg_match('@[0-9]@', $password_candidate);
        $has_letters = preg_match('@[a-zA-Z]@', $password_candidate);
        if (!$has_number || (!$has_letters)) {
            $route->setError('password', PASSWORD_WEAK);
            return false;
        }
        return true;
    }
}
