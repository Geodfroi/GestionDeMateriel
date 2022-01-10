<?php

################################
## Joël Piguet - 2022.01.10 ###
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
        parent::__construct(Route::LOGIN, 'login_template');
    }

    public function getBodyContent(): string
    {
        if (Authenticate::isLoggedIn()) {

            if (isset($_GET['logout'])) {
                Authenticate::logout();
                $this->requestRedirect(Route::LOGIN);
            } else {
                $this->requestRedirect(Route::HOME);
            }
            return '';
        }

        if (isset($_GET['old-email'])) {

            // handle demand for new password.
            $login_email  = $_GET['old-email'];
            $user = Database::users()->queryByEmail($login_email);

            if (isset($user)) {
                if (Util::renewPassword($user)) {
                    return $this->requestRedirect(Route::LOGIN, AlertType::SUCCESS, printf(Alert::NEW_PASSWORD_SUCCESS, $user->getLoginEmail()));
                }
                return $this->requestRedirect(Route::LOGIN, AlertType::FAILURE, Alert::NEW_PASSWORD_FAILURE);
            }

            goto end;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            if (Validation::validateLoginEmail($this, $login_email)) {
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
