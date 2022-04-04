<?php

declare(strict_types=1);

use app\constants\Route;
use app\constants\Warning;
use app\helpers\Authenticate;
use app\helpers\BaseRoute;
use app\helpers\Database;
use app\helpers\Util;
use app\helpers\Logging;
use app\helpers\RequestUtil;

################################
## Joël Piguet - 2022.04.04 ###
##############################

require_once __DIR__ . '/../loader.php';
require_once __DIR__ . '/../vendor/autoload.php'; // use composer to load autofile.
session_start();

const ROUTE_FOLDER = APP_URL . 'login';

/**
 * Route class containing behavior linked to login_template
 */
class Login extends BaseRoute
{
    public function __construct()
    {
        parent::__construct('login', 'login_template', 'login_script');
    }

    public function getBodyContent(): string
    {
        return $this->renderTemplate();
    }
}

function loginattempt($json): string
{
    $login = isset($json['login']) ? $json['login'] : "";
    $password = isset($json['password']) ? $json['password'] : "";
    $json['display_renew_btn'] = false;
    $warnings = [];

    if ($login  === '') {
        $warnings['login'] =  Warning::LOGIN_EMPTY;
        $warnings['password'] =  Warning::LOGIN_EMPTY;
        return RequestUtil::issueWarnings($json, $warnings);
    }

    $email = filter_var($login, FILTER_VALIDATE_EMAIL);
    $user = null;

    if ($email) {
        $user = Database::users()->queryByEmail($email);
        if (!$user) {
            $warnings['login'] = Warning::LOGIN_EMAIL_NOT_FOUND;
        }
    } else {
        $user = Database::users()->queryByAlias($login);
        if (!$user) {
            $warnings['login'] = Warning::LOGIN_ALIAS_NOT_FOUND;
        }
    }

    if ($password === '') {
        $warnings['password'] = Warning::LOGIN_PASSWORD_EMPTY;
    } else {
        if ($user) {
            $json['display_renew'] = true;
            if ($user->verifyPassword($password)) {
                Authenticate::login($user);
                return RequestUtil::redirect(Route::HOME);
            } else {
                $warnings['password'] = Warning::LOGIN_INVALID_PASSWORD;
            }
        }
    }
    return RequestUtil::issueWarnings($json, $warnings);
}


Logging::debug("login route");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $json = RequestUtil::retrievePOSTData();
    Logging::debug("login POST request", $json);
    echo loginattempt($json);
} else {
    if (Authenticate::isLoggedIn()) {
        Util::requestRedirect(Route::HOME);
    } else {
        echo (new Login())->renderRoute();
    }
}
