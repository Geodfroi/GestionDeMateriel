<?php

declare(strict_types=1);

################################
## Joël Piguet - 2021.12.13###
##############################

namespace app\helpers;

use DateTime;

use app\constants\Settings;
use app\constants\Warning;
// use app\helpers\Logging;
use app\routes\BaseRoute;

class Validation
{
    /**
     * Validate user alias. Alias can be set to empty string in which cas e-mail root is used in the app.
     * 
     * @param BaseRoute $route Route to forward error messages. 
     * @param string|null $alias Optional alias by reference.
     * @return bool True if Alias is conform or empty.
     */
    public static function validateAlias(BaseRoute $route, &$alias): bool
    {
        $alias = trim($_POST['alias']) ?? '';
        if ($alias === '') {
            return true;
        }
        if (strlen($alias) < Settings::ALIAS_MIN_LENGHT) {
            $route->setError('alias', sprintf(Warning::ALIAS_TOO_SHORT, Settings::ALIAS_MIN_LENGHT));
            return false;
        }
        return true;
    }

    /**
     * Article name validation. Article name must not be empty, exceed a set length and under a set number of caracters.
     * 
     * @param BaseRoute $route Route to forward error messages. 
     * @param array &$string $article_name Article name by reference.
     * @return bool True if validated.
     */
    public static function validateArticleName(BaseRoute $route, ?string &$article_name): bool
    {
        $article_name = trim($_POST['article-name']) ?? '';

        if (
            $article_name === ''
        ) {
            $route->setError('article-name', Warning::ARTICLE_ADD_EMPTY);
            return false;
        }

        if (strlen($article_name) < Settings::ARTICLE_NAME_MIN_LENGHT) {
            $route->setError('article-name', sprintf(Warning::ARTICLE_NAME_TOO_SHORT, Settings::ARTICLE_NAME_MIN_LENGHT));
            return false;
        }

        if (strlen($article_name) > Settings::ARTICLE_NAME_MAX_LENGTH) {
            $route->setError('article-name', sprintf(Warning::ARTICLE_NAME_TOO_LONG, Settings::ARTICLE_NAME_MAX_LENGTH));
            return false;
        }
        return true;
    }

    /**
     * Comments validation. Comments can be empty string but be under a set number of caracters.
     * 
     * @param BaseRoute $route Route to forward error messages. 
     * @param string &$comments Comments to be attached to the reminder by reference.
     * @return bool True if validated.
     */
    public static function validateComments(BaseRoute $route, ?string &$comments): bool
    {
        $comments = trim($_POST['comments']) ?? '';

        if (strlen($comments) > Settings::ARTICLE_COMMENTS_MAX_LENGHT) {
            $route->setError('comments', sprintf(Warning::COMMENTS_NAME_TOO_LONG, Settings::ARTICLE_COMMENTS_MAX_LENGHT));
            return false;
        }
        return true;
    }

    /**
     * Validate contact email. Email must a valid email format or set to null.
     * 
     * @param BaseRoute $route Route to forward error messages. 
     * @param string|null $contact_email Contact e-mail by reference.
     * @return bool True if e-mail is set to empty string or is a valid email format.
     */
    public static function validateContactEmail(BaseRoute $route, &$email): bool
    {
        $email = trim($_POST['contact-email']) ?? '';

        if ($email  === '') {
            return true;
        }
        $email = filter_var($email, FILTER_VALIDATE_EMAIL);
        if (!$email) {
            $route->setError('contact-email', Warning::LOGIN_EMAIL_INVALID);
            return false;
        }
        return true;
    }

    /**
     * Date validation. Date must not be empty and correspond to format yyyy-mm-dd
     * 
     * @param BaseRoute $route Route to forward error messages. 
     * @param string &$validated_date Validated expiration date.
     * @return bool True if validated.
     */
    public static function validateExpirationDate(BaseRoute $route, ?string &$date): bool
    {
        $date = trim($_POST['expiration-date'] ?? '');

        if ($date === '') {
            $route->setError('expiration-date', Warning::DATE_EMPTY);
            return false;
        }

        $validated_date = DateTime::createFromFormat('Y-m-d', $date);
        $date = $validated_date->format('Y-m-d');

        static $future_limit;
        if (is_null($future_limit)) {
            $future_limit = DateTime::createFromFormat('Y-m-d', Settings::ARTICLE_DATE_FUTURE_LIMIT);
        }

        if ($validated_date) {

            if ($validated_date < new DateTime()) {
                $route->setError('expiration-date', Warning::DATE_PAST);
                return false;
            }

            if ($validated_date >= $future_limit) {
                $route->setError('expiration-date', Warning::DATE_FUTURE);
                return false;
            }

            return true;
        }

        $route->setError('expiration-date', Warning::DATE_INVALID);
        return false;
    }

    /**
     * Validate input and fill $errors array with proper email error text to be displayed if it fails.
     * 
     * @param BaseRoute $route Route to forward error messages. 
     * @param ?string $email User email by reference.
     * @return bool True if properly filled-in.
     */
    public static function validateLoginEmail(BaseRoute $route, ?string &$email): bool
    {
        $email = trim($_POST['email']) ?? '';

        if ($email  === '') {
            $route->setError('email', Warning::LOGIN_EMAIL_EMPTY);
            return false;
        }
        $email = filter_var($email, FILTER_VALIDATE_EMAIL);
        if (!$email) {
            $route->setError('email', Warning::LOGIN_EMAIL_INVALID);
            return false;
        }
        return true;
    }

    /**
     * Validate input and fill $errors array with proper password error text to be displayed if it fails.
     * 
     * @param BaseRoute $route Route to forward error messages. 
     * @param string $password User password by reference
     * @return bool True if properly filled;
     */
    public static function validateLoginPassword(BaseRoute $route, &$password)
    {
        $password = trim($_POST['password']) ?? '';
        if ($password === '') {
            $route->setError('password', Warning::LOGIN_PASSWORD_EMPTY);
            return false;
        }
        return true;
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
            $route->setError('location', Warning::LOCATION_EMPTY);
            return false;
        }

        $location = ucwords($location);
        if (strlen($location) < Settings::LOCATION_MIN_LENGHT) {
            $route->setError('location', sprintf(Warning::LOCATION_TOO_SHORT, Settings::LOCATION_MIN_LENGHT));
            return false;
        }

        if (strlen($location) > Settings::LOCATION_MAX_LENGHT) {
            $route->setError('location', sprintf(Warning::LOCATION_TOO_LONG, Settings::LOCATION_MAX_LENGHT));
            return false;
        }
        return true;
    }

    /**
     * Validate input and fill $errors array with proper email error text to be displayed if it fails.
     * 
     * @param BaseRoute $route Route to forward error messages. 
     * @param ?string $email User email by reference.
     * @return bool True if properly filled-in.
     */
    public static function validateNewLogin(BaseRoute $route, ?string &$email): bool
    {
        if (!Validation::validateLoginEmail($route, $email)) {
            return false;
        }

        if (Database::users()->queryByEmail($email)) {
            $route->setError('email', Warning::USER_EMAIL_USED);
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
    public static function validateNewPassword(BaseRoute $route, ?string &$password_candidate): bool
    {
        $password_candidate = trim($_POST['password']) ?? '';
        if ($password_candidate === '') {
            $route->setError('password', Warning::PASSWORD_EMPTY);
            return false;
        }
        if (strlen($password_candidate) < Settings::USER_PASSWORD_MIN_LENGTH) {
            $route->setError('password', sprintf(Warning::PASSWORD_SHORT, Settings::USER_PASSWORD_MIN_LENGTH));
            return false;
        }
        $has_number = preg_match('@[0-9]@', $password_candidate);
        $has_letters = preg_match('@[a-zA-Z]@', $password_candidate);
        if (!$has_number || (!$has_letters)) {
            $route->setError('password', Warning::PASSWORD_WEAK);
            return false;
        }
        return true;
    }

    /**
     * Validate the repeated password.
     * 
     * @param BaseRoute $route Route to forward error messages. 
     * @param string $password_first Proposed user password entered in first field.
     * @return bool True if repeat-password corresponds to first entry.
     */
    public static function validateNewPasswordRepeat(BaseRoute $route, string $password_first): bool
    {
        $password_repeat = trim($_POST['password-repeat']) ?? '';
        if (!$password_repeat) {
            $route->setError('password-repeat', Warning::PASSWORD_REPEAT_NULL);
            return false;
        }

        if ($password_first !== $password_repeat) {
            $route->setError('password-repeat', Warning::PASSWORD_DIFFERENT);
            return false;
        }
        return true;
    }
}
