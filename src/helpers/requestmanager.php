<?php

declare(strict_types=1);

################################
## Joël Piguet - 2022.01.11 ###
##############################

namespace app\helpers;

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


/**
 * Handle fetch requests for data from javascript.
 */
class RequestManager
{
    public static function fetchData(): string
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return "";
        }
        // Takes raw data from the request
        $json = file_get_contents('php://input');
        // Converts it into a PHP object
        $data = json_decode($json, true);

        if (!isset($data['req'])) {
            $response = ['error' => '[req] key was not defined in fetch request.'];
            Logging::error('data request error', $response);
            return json_encode($response);
        }

        switch ($data['req']) {
            case 'post-profile-alias':
                return RequestManager::updateAlias($data);

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
        if (strlen($alias) < Settings::ALIAS_MIN_LENGHT) {
            return RequestManager::issueWarnings(['alias' => sprintf(Warning::ALIAS_TOO_SHORT, Settings::ALIAS_MIN_LENGHT)]);
            // return json_encode(['alias' => sprintf(Warning::ALIAS_TOO_SHORT, Settings::ALIAS_MIN_LENGHT)]);
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
            Util::storeAlert($alert_type, $alert_msg, $url);
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
