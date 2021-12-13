<?php

################################
## Joël Piguet - 2021.12.13 ###
##############################

namespace app\routes;

use app\constants\Alert;
use app\constants\AlertType;
use app\constants\Route;
use app\constants\Warning;
use app\helpers\Authenticate;
use app\helpers\Database;
use app\helpers\Util;
use app\helpers\Validation;

/**
 * Route class containing behavior linked to login_template
 */
class Login extends BaseRoute
{
    public function __construct()
    {
        parent::__construct('login_template', Route::LOGIN);
    }

    public function getBodyContent(): string
    {
        if (Authenticate::isLoggedIn()) {

            if (isset($_GET['logout'])) {
                Authenticate::logout();
                $this->setAlert(AlertType::INFO, Alert::LOGIN_USER_DISC);
            } else {
                $this->requestRedirect(Route::HOME);
                return '';
            }
        }

        if (isset($_GET['old-email'])) {

            // handle demand for new password.
            $email  = $_GET['old-email'];
            $user = Database::users()->queryByEmail($email);

            if (isset($user)) {
                Util::renewPassword($this, $user);
            }

            goto end;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            if (Validation::validateLoginEmail($this, $email)) {
                $user = Database::users()->queryByEmail($email);
            }

            if (!isset($user)) {
                $this->setError('email', Warning::LOGIN_NOT_FOUND);
            } else {
                if (Validation::validateLoginPassword($this, $password)) {
                    if ($user->verifyPassword($password)) {
                        Authenticate::login($user);
                        $this->requestRedirect(Route::HOME);
                        return "";
                    } else {
                        $this->setError('password', Warning::LOGIN_INVALID_PASSWORD);
                    }
                }
            }
        }

        end:
        return $this->renderTemplate([
            'email' => $email ?? '',
            'password' => $password ?? '',
        ]);
    }
}
