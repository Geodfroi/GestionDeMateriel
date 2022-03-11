<?php

declare(strict_types=1);

################################
## Joël Piguet - 2022.01.17 ###
##############################

namespace app\helpers;

use DateTime;

use app\constants\Settings;
use app\constants\Warning;

// use app\helpers\Logging;

class Validation
{
    /**
     * Article name validation. Article name must not be empty, exceed a set length and under a set number of caracters.
     * 
     * @param array &$string $article_name Article name by reference.
     * @return string Empty string if value is validated or warning message in case of failure.
     */
    public static function validateArticleName(string &$article_name): string
    {
        if ($article_name === '') {
            return Warning::ARTICLE_ADD_EMPTY;
        }

        if (strlen($article_name) < ARTICLE_NAME_MIN_LENGHT) {
            return sprintf(Warning::ARTICLE_NAME_TOO_SHORT, ARTICLE_NAME_MIN_LENGHT);
        }

        if (strlen($article_name) > ARTICLE_NAME_MAX_LENGTH) {
            return sprintf(Warning::ARTICLE_NAME_TOO_LONG, ARTICLE_NAME_MAX_LENGTH);
        }
        return "";
    }

    /**
     * Comments validation. Comments can be empty string but be under a set number of caracters.
     * 
     * @param string &$comments Comments to be attached to the reminder by reference.
     * @return string Empty string if value is validated or warning message in case of failure.
     */
    public static function validateComments(string &$comments): string
    {
        if (strlen($comments) > ARTICLE_COMMENTS_MAX_LENGHT) {
            return sprintf(Warning::COMMENTS_NAME_TOO_LONG, ARTICLE_COMMENTS_MAX_LENGHT);
        }
        return "";
    }

    /**
     * Validate contact email. Email must a valid email format or set to null.
     * 
     * @param string $contact_email Contact e-mail candidate.
     * @return string Empty string if value is validated or warning message in case of failure.
     */
    public static function validateContactEmail(string $email): string
    {
        if ($email  === '') {
            return "";
        }
        $email = filter_var($email, FILTER_VALIDATE_EMAIL);
        if (!$email) {
            return Warning::LOGIN_EMAIL_INVALID;
        }
        return "";
    }

    /**
     * Date validation. Date must not be empty and correspond to format yyyy-mm-dd
     * 
     * @param string &$validated_date Validated expiration date.
     * @return string Empty string if value is validated or warning message in case of failure.
     */
    public static function validateExpirationDate(string &$date): string
    {
        if ($date === '') {
            return Warning::DATE_EMPTY;
        }

        // verify than the date can be created from format.
        $validated_date = DateTime::createFromFormat('Y-m-d', $date);
        if (!$validated_date) {
            return Warning::DATE_INVALID;
        }

        $date = $validated_date->format('Y-m-d');

        if ($validated_date < new DateTime()) {
            return Warning::DATE_PAST;
        }

        static $future_limit;
        if (is_null($future_limit)) {
            $future_limit = DateTime::createFromFormat('Y-m-d', ARTICLE_DATE_FUTURE_LIMIT);
        }

        if ($validated_date >= $future_limit) {
            return Warning::DATE_FUTURE;
        }

        return "";
    }

    /**
     * Location validation. Location must not be empty and under a set number of caracters.
     * 
     * @param string &$location Article's location within the school by reference.
     * @return string Empty string if value is validated or warning message in case of failure.
     */
    public static function validateLocation(string &$location): string
    {
        if (strlen($location) === 0) {
            return  Warning::LOCATION_EMPTY;
        }

        $location = ucwords($location);
        if (strlen($location) < ARTICLE_LOCATION_MIN_LENGHT) {
            return sprintf(Warning::LOCATION_TOO_SHORT, ARTICLE_LOCATION_MIN_LENGHT);
        }

        if (strlen($location) > ARTICLE_LOCATION_MAX_LENGHT) {
            return sprintf(Warning::LOCATION_TOO_LONG, ARTICLE_LOCATION_MAX_LENGHT);
        }
        return "";
    }

    /**
     * Location preset validation. Location must not be empty, under a set number of caracters and not yet exist.
     * 
     * @param string &$location Article's location within the school by reference.
     * @return string Empty string if value is validated or warning message in case of failure.
     */
    public static function validateLocationPreset(string &$location): string
    {
        if ($warning = Validation::validateLocation($location)) {
            return $warning;
        }
        if (Database::locations()->contentExists($location)) {
            return Warning::LOCATION_PRESET_EXISTS;
        }
        return "";
    }

    /**
     * Validate input and fill $errors array with proper error text to be displayed if it fails.
     * 
     * @param ?string $email User email by reference.
     * @return string Empty string if value is validated or warning message in case of failure.
     */
    public static function validateNewLogin(?string &$email): string
    {
        if ($email  === '') {
            return Warning::LOGIN_EMAIL_EMPTY;
        }

        $email = filter_var($email, FILTER_VALIDATE_EMAIL);
        if (!$email) {
            return Warning::LOGIN_EMAIL_INVALID;
        }

        if (Database::users()->queryByEmail($email)) {
            return Warning::USER_EMAIL_USED;
        }
        return "";
    }

    /**
     * Validate input and fill $warning array with proper warning to be displayed in case of failure.
     * https://www.codexworld.com/how-to/validate-password-strength-in-php/
     * 
     * @param string $password_candidate Proposed user password by reference.
     * @return string Empty string if value is validated or warning message in case of failure.
     */
    public static function validateNewPassword(string &$password_candidate): string
    {
        if ($password_candidate === '') {
            return Warning::PASSWORD_EMPTY;
        }
        if (strlen($password_candidate) < USER_PASSWORD_MIN_LENGTH) {
            return sprintf(Warning::PASSWORD_SHORT, USER_PASSWORD_MIN_LENGTH);
        }
        $has_number = preg_match('@[0-9]@', $password_candidate);
        $has_letters = preg_match('@[a-zA-Z]@', $password_candidate);
        if (!$has_number || (!$has_letters)) {
            return Warning::PASSWORD_WEAK;
        }
        return "";
    }

    /**
     * Validate the repeated password.
     * 
     * @param string $password_first Proposed user password entered in first field.
     * @param string $password_repeat Repeat of proposed password to filter typing mishaps.
     * @return string Empty string if value is validated or warning message in case of failure.
     */
    public static function validateNewPasswordRepeat(string $password_first, string $password_repeat): string
    {
        if (!$password_repeat) {
            return Warning::PASSWORD_REPEAT_NULL;
        }

        if ($password_first !== $password_repeat) {
            return Warning::PASSWORD_DIFFERENT;
        }
        return "";
    }
}
