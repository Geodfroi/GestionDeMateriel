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

        end:
        return $this->renderTemplate([
            'login_email' => $login_email ?? '',
            'password' => $password ?? '',
        ]);
    }
}
