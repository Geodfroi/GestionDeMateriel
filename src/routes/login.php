<?php

################################
## Joël Piguet - 2022.01.13 ###
##############################

namespace app\routes;

use app\constants\Alert;
use app\constants\AlertType;
use app\constants\Route;
use app\constants\Warning;
use app\helpers\Authenticate;
use app\helpers\Database;
// use app\helpers\Logging;
use app\helpers\Util;
use app\helpers\Validation;

/**
 * Route class containing behavior linked to login_template
 */
class Login extends BaseRoute
{
    public function __construct()
    {
        parent::__construct(Route::LOGIN, 'login_template', 'login_script');
    }

    public function getBodyContent(): string
    {
        if (Authenticate::isLoggedIn()) {
            return $this->requestRedirect(Route::HOME);
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $login_email = trim($_POST['login-email']) ?? '';

            if ($warning = Validation::validateLoginEmail($login_email)) {
                $this->showWarning('login-email', $warning);
            } else {
                $user = Database::users()->queryByEmail($login_email);
            }

            if (!isset($user)) {
                $this->showWarning('login-email', Warning::LOGIN_NOT_FOUND);
            } else {
                if (Validation::validateLoginPassword($this, $password)) {
                    if ($user->verifyPassword($password)) {
                        Authenticate::login($user);
                        $this->requestRedirect(Route::HOME);
                        return "";
                    } else {
                        $this->showWarning('password', Warning::LOGIN_INVALID_PASSWORD);
                    }
                }
            }
        }

        end:
        return $this->renderTemplate([
            'login_email' => $login_email ?? '',
            'password' => $password ?? '',
        ]);
    }
}
