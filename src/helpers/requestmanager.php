<?php

declare(strict_types=1);

################################
## Joël Piguet - 2022.01.13 ###
##############################

namespace app\helpers;

use app\constants\Requests;
use app\constants\Alert;
use app\constants\AlertType;
use app\constants\LogInfo;
use app\constants\Route;
use app\constants\Settings;
use app\constants\Warning;
use app\helpers\Authenticate;
use app\helpers\Database;
use app\helpers\Logging;
use app\helpers\Util;
use app\helpers\Validation;
use Exception;
use SebastianBergmann\Environment\Console;

/**
 * Handle fetch requests for data from javascript.
 */
class RequestManager
{
    /**
     * Answer for call requests by browser. 
     */
    public static function call(): string
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            return RequestManager::handleGetRequests();
        } else if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            return RequestManager::handlePostRequests();
        }
        Logging::error('Invalid request call');
        return null;
    }

    /**
     * Note: Get requests use Util::requestRedirect for redirection (use header php function to signal browser)
     */
    private static function handleGetRequests(): string
    {
        if (App::isDebugMode()) {
            Logging::info("Get request to server", ['args' => $_GET]);
        }

        if (isset($_GET['logout'])) {
            Logging::debug('logout');
            Authenticate::logout();
            header("Location: /login", true);
            return "";
        }

        if (isset($_GET['renewpassword'])) {
            Logging::debug('renewpassword');
            // handle demand for new password.
            $login_email  = trim($_GET['renewpassword']);
            $user = Database::users()->queryByEmail($login_email);

            if (isset($user)) {
                if (Util::renewPassword($user)) {
                    return Util::requestRedirect(
                        Route::LOGIN,
                        AlertType::SUCCESS,
                        sprintf(Alert::NEW_PASSWORD_SUCCESS, $user->getLoginEmail())
                    );
                }
            }
            return Util::requestRedirect(
                Route::LOGIN,
                AlertType::FAILURE,
                Alert::NEW_PASSWORD_FAILURE
            );
        }
        Logging::error("Invalid get request to server", ['args' => $_GET]);
        return "Invalid get request to server";
    }

    /**
     * Note: Post requests use RequestManager::redirect for redirection (signal javascript to redirect)
     */
    private static function handlePostRequests(): string
    {
        // Takes raw data from the request
        $json = file_get_contents('php://input');
        // Converts it into a PHP object
        $data = json_decode($json, true);
        Logging::debug('fetch data', $data);

        if (!isset($data['req'])) {
            $response = ['error' => '[req] key was not defined in fetch request.'];
            Logging::error('data request error', $response);
            return json_encode($response);
        }

        if (App::isDebugMode()) {
            Logging::info("Post request to server", ['data' => $data['req']]);
        }

        switch ($data['req']) {

            case 'get-user':
                Logging::debug('get-user');
                $user = Authenticate::getUser()->toJSON();
                Logging::debug($user);
                return Authenticate::getUser()->toJSON();
            case 'regen-password':
                return RequestManager::regenPassword();

            case 'update-alias':
                return RequestManager::updateAlias($data);
            case 'update-delay':
                return RequestManager::updateDelays($data);
            case 'update-password':
                return RequestManager::updatePassword($data);
            case "update-contact-email":
                return RequestManager::updateContactEmail($data);

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

    private static function regenPassword()
    {
        return json_encode(['password' => Util::getRandomPassword()]);
    }

    private static function updateAlias($json): string
    {
        $user  = Authenticate::getUser();
        if (!$user) {
            return RequestManager::redirect(Route::HOME);
        }

        $user_id = $user->getId();
        $alias = $json["alias"];

        // alias was not actually changed
        if ($alias === $user->getAlias()) {
            return RequestManager::redirect(Route::PROFILE);
        }

        //validate alias
        if (strlen($alias) > 0 && strlen($alias) < Settings::ALIAS_MIN_LENGHT) {
            return RequestManager::issueWarnings(['alias' => sprintf(Warning::ALIAS_TOO_SHORT, Settings::ALIAS_MIN_LENGHT)]);
        }
        $alias_arg = $alias ? $alias : $user->getLoginEmail();
        if ($existing_user = Database::users()->queryByAlias($alias_arg)) {
            if ($existing_user->getId() !== $user_id) {
                // alias already exists and assigned to another user.
                return RequestManager::issueWarnings(['alias' => Warning::ALIAS_ALREADY_EXISTS]);
            }
        }

        if (Database::users()->updateAlias($user_id, $alias_arg)) {

            Logging::info(LogInfo::USER_UPDATED, [
                'user-id' => $user_id,
                'new-alias' => $alias_arg
            ]);

            if ($alias) {
                return RequestManager::redirect(Route::PROFILE, AlertType::SUCCESS, Alert::ALIAS_UPDATE_SUCCESS);
            }
            return RequestManager::redirect(Route::PROFILE, AlertType::SUCCESS, Alert::ALIAS_DELETE_SUCCESS);
        }
        return RequestManager::redirect(Route::PROFILE, AlertType::FAILURE, Alert::ALIAS_UPDATE_FAILURE);
    }

    private static function updateDelays($json): string
    {
        $user  = Authenticate::getUser();
        if (!$user) {
            return RequestManager::redirect(Route::HOME);
        }

        $user_id = $user->getId();
        $delay = $json["delay"];

        if (!$delay) {
            Logging::debug('warning', ['delay' => Warning::DELAYS_NONE]);
            return RequestManager::issueWarnings(['delay' => Warning::DELAYS_NONE]);
        }

        if (Database::users()->updateContactDelay($user_id, $delay)) {

            Logging::info(LogInfo::USER_UPDATED, [
                'user-id' => $user_id,
                'new-contact-delays' => $delay
            ]);

            return RequestManager::redirect(Route::PROFILE, AlertType::SUCCESS, Alert::DELAY_SET_SUCCESS);
        }
        return RequestManager::redirect(
            Route::PROFILE,
            AlertType::FAILURE,
            Alert::DELAY_SET_FAILURE
        );
    }

    private static function updateContactEmail($json): string
    {
        $user  = Authenticate::getUser();
        if (!$user) {
            return RequestManager::redirect(Route::HOME);
        }

        $user_id = $user->getId();
        $contact_email = $json["contact-email"];


        if ($warning = Validation::validateContactEmail($contact_email)) {
            return RequestManager::issueWarnings(['contact-email' => $warning]);
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
                return RequestManager::redirect(Route::PROFILE, AlertType::SUCCESS, sprintf(Alert::CONTACT_SET_SUCCESS, $contact_email));
            }
            return RequestManager::redirect(
                Route::PROFILE,
                AlertType::SUCCESS,
                sprintf(Alert::CONTACT_RESET_SUCCESS, $user->getLoginEmail())
            );
        }
        return RequestManager::redirect(
            Route::PROFILE,
            AlertType::FAILURE,
            Alert::CONTACT_SET_FAILURE
        );
    }

    private static function updatePassword($json): string
    {
        $user = Authenticate::getUser();
        if (!$user) {

            return RequestManager::redirect(Route::HOME);
        }
        $user_id = $user->getId();
        $password_plain = $json["password"];
        $password_repeat = $json["password-repeat"];
        $warnings = [];

        $val = Validation::validateNewPassword($password_plain);
        $val_repeat = Validation::validateNewPasswordRepeat($password_plain, $password_repeat);

        if ($val) {
            $warnings['password'] = $val;
        }
        if ($val_repeat) {
            $warnings['password-repeat'] = $val_repeat;
        }

        if ($val || $val_repeat) {
            return RequestManager::issueWarnings($warnings);
        }

        $encrypted = util::encryptPassword($password_plain);

        if (Database::users()->updatePassword($user_id, $encrypted)) {

            Logging::info(LogInfo::USER_UPDATED, [
                'user-id' => $user_id,
                'new-password' => '*********'
            ]);
            return RequestManager::redirect(Route::PROFILE, AlertType::SUCCESS, Alert::PASSWORD_UPDATE_SUCCESS);
        }
        return RequestManager::redirect(Route::PROFILE, AlertType::FAILURE, Alert::PASSWORD_UPDATE_FAILURE);
    }

    /**
     * Instruct js fetch function to redirect to url.
     * 
     * @param string $url The redirection path. Use the constants in Routes class to avoir typing mistakes.
     * @param string $alert_type Optional alert type. Use AlertType const.
     * @param string $alert_msg Optional alert message to be displayed after redirection.
     * @return string json response.
     */
    private static function redirect(string $url, string $alert_type = "", string $alert_msg = ""): string
    {
        if (strlen($alert_type) != 0 && strlen($alert_msg) != 0) {
            Util::sstoreAlert($url, $alert_type, $alert_msg);
        }
        return json_encode(['url' => $url]);
    }

    /**
     * @param string $warnings. Warnings associative array with input field as key.
     * @return string json response.
     */
    private static function issueWarnings(array $warnings): string
    {
        return json_encode([
            'validated' => false,
            'warnings' => $warnings,
        ]);
    }
}
