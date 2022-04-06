<?php

declare(strict_types=1);

################################
## Joël Piguet - 2022.04.06 ###
##############################
/**
 * This route handle app fetch requests for data from javascript.
 */

use app\constants\Alert;
use app\constants\AlertType;
use app\constants\LogInfo;
use app\constants\Route;
use app\constants\Warning;
use app\helpers\Authenticate;
use app\helpers\Database;
use app\helpers\Logging;
use app\helpers\RequestUtil;
use app\helpers\Util;
use app\helpers\Validation;


require_once __DIR__ . '/../loader.php';
require_once __DIR__ . '/../vendor/autoload.php'; // use composer to load autofile.
session_start();

Logging::debug('server data', $_SERVER);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    echo handlePostRequests();
} else {
    Logging::error('Invalid request call');
    echo 'Invalid request call';
}

/**
 * Note: Post requests use redirect for redirection (signal javascript to redirect)
 */
function handlePostRequests(): string
{
    // Takes raw data from the request
    $json = file_get_contents('php://input');
    // Converts it into a PHP object
    $data = json_decode($json, true);

    if (!isset($data['req'])) {
        $response = ['error' => '[req] key was not defined in fetch request.'];
        Logging::error('data request error', $response);
        return json_encode($response);
    }

    Logging::debug("Post request to server", ['data' => $data['req']]);

    switch ($data['req']) {
        case 'get-article':
            $article = Database::articles()->queryById(intval($data['id']));
            return json_encode($article->asArray());
        case 'get-user':
            return Authenticate::getUser()->toJSON();
        case 'regen-password':
            return regenPassword();
        case 'update-article':
            return updateArticle($data);
        case 'update-alias':
            return updateAlias($data);
        case 'update-delay':
            return updateDelays($data);
        case 'update-password':
            return updatePassword($data);
        case "update-contact-email":
            return updateContactEmail($data);
        case 'validate-user':
            return validateNewUser($data);
        default:
            $response = [
                'error' => '[req] key was not found in fetch request.',
                'req' => $data['req']
            ];
            Logging::error('data request error', $response);
            return json_encode($response);
    }
    return json_encode($data);
}

function updateAlias($json): string
{
    $user  = Authenticate::getUser();
    if (!$user) {
        return RequestUtil::redirectJSON(Route::HOME);
    }

    $user_id = $user->getId();
    $alias = isset($json["alias"]) ? $json["alias"] : "";

    // alias was not actually changed
    if ($alias === $user->getAlias()) {
        return RequestUtil::redirectJSON(Route::PROFILE);
    }

    //validate alias
    if (strlen($alias) > 0 && strlen($alias) < USER_ALIAS_MIN_LENGHT) {
        return RequestUtil::issueWarnings($json, ['alias' => sprintf(Warning::ALIAS_TOO_SHORT, USER_ALIAS_MIN_LENGHT)]);
    }
    $alias_arg = $alias ? $alias : $user->getLoginEmail();
    if ($existing_user = Database::users()->queryByAlias($alias_arg)) {
        if ($existing_user->getId() !== $user_id) {
            // alias already exists and assigned to another user.
            return RequestUtil::issueWarnings($json, ['alias' => Warning::ALIAS_ALREADY_EXISTS]);
        }
    }

    if (Database::users()->updateAlias($user_id, $alias_arg)) {

        Logging::info(LogInfo::USER_UPDATED, [
            'user-id' => $user_id,
            'new-alias' => $alias_arg
        ]);

        if ($alias) {
            return RequestUtil::redirectJSON(Route::PROFILE, AlertType::SUCCESS, Alert::ALIAS_UPDATE_SUCCESS);
        }
        return RequestUtil::redirectJSON(Route::PROFILE, AlertType::SUCCESS, Alert::ALIAS_DELETE_SUCCESS);
    }
    return RequestUtil::redirectJSON(Route::PROFILE, AlertType::FAILURE, Alert::ALIAS_UPDATE_FAILURE);
}



function updateDelays($json): string
{
    $user  = Authenticate::getUser();
    if (!$user) {
        return RequestUtil::redirectJSON(Route::HOME);
    }

    $user_id = $user->getId();
    $delay = isset($json["delay"]) ?  $json["delay"] : "";

    if (!$delay) {
        Logging::debug('warning', ['delay' => Warning::DELAYS_NONE]);
        return RequestUtil::issueWarnings($json, ['delay' => Warning::DELAYS_NONE]);
    }

    if (Database::users()->updateContactDelay($user_id, $delay)) {

        Logging::info(LogInfo::USER_UPDATED, [
            'user-id' => $user_id,
            'new-contact-delays' => $delay
        ]);

        return RequestUtil::redirectJSON(Route::PROFILE, AlertType::SUCCESS, Alert::DELAY_SET_SUCCESS);
    }
    return RequestUtil::redirectJSON(
        Route::PROFILE,
        AlertType::FAILURE,
        Alert::DELAY_SET_FAILURE
    );
}

function updateContactEmail($json): string
{
    $user  = Authenticate::getUser();
    if (!$user) {
        return RequestUtil::redirectJSON(Route::HOME);
    }

    $user_id = $user->getId();
    $contact_email = isset($json["contact-email"]) ? $json["contact-email"] : "";

    if ($warning = Validation::validateContactEmail($contact_email)) {
        return RequestUtil::issueWarnings($json, ['contact-email' => $warning]);
    }

    if ($contact_email === $user->getLoginEmail()) {
        $contact_email  = '';
    }

    if (Database::users()->updateContactEmail($user_id, $contact_email)) {

        Logging::info(LogInfo::USER_UPDATED, [
            'user-id' => $user_id,
            'new-contact-email' => $contact_email
        ]);

        // if contact is null or empty, then contact is the login email.
        if (strlen($contact_email) > 0) {
            return RequestUtil::redirectJSON(Route::PROFILE, AlertType::SUCCESS, sprintf(Alert::CONTACT_SET_SUCCESS, $contact_email));
        }
        return RequestUtil::redirectJSON(
            Route::PROFILE,
            AlertType::SUCCESS,
            sprintf(Alert::CONTACT_RESET_SUCCESS, $user->getLoginEmail())
        );
    }
    return RequestUtil::redirectJSON(
        Route::PROFILE,
        AlertType::FAILURE,
        Alert::CONTACT_SET_FAILURE
    );
}

function updatePassword($json): string
{
    $user = Authenticate::getUser();
    if (!$user) {
        return RequestUtil::redirectJSON(Route::HOME);
    }
    $user_id = $user->getId();
    $password_plain = isset($json["password"]) ? $json["password"] : "";
    $password_repeat = isset($json["password-repeat"]) ? $json["password-repeat"] : "";
    $warnings = [];

    $password_warning = Validation::validateNewPassword($password_plain);
    $password_warning_repeat = Validation::validateNewPasswordRepeat($password_plain, $password_repeat);

    if ($password_warning) {
        $warnings['password'] = $password_warning;
    }
    if ($password_warning_repeat) {
        $warnings['password-repeat'] = $password_warning_repeat;
    }

    if ($password_warning || $password_warning_repeat) {
        return RequestUtil::issueWarnings($json, $warnings);
    }

    $encrypted = util::encryptPassword($password_plain);

    if (Database::users()->updatePassword($user_id, $encrypted)) {

        Logging::info(LogInfo::USER_UPDATED, [
            'user-id' => $user_id,
            'new-password' => '*********'
        ]);
        return RequestUtil::redirectJSON(Route::PROFILE, AlertType::SUCCESS, Alert::PASSWORD_UPDATE_SUCCESS);
    }
    return RequestUtil::redirectJSON(Route::PROFILE, AlertType::FAILURE, Alert::PASSWORD_UPDATE_FAILURE);
}
