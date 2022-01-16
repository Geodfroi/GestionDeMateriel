<?php

################################
## Joël Piguet - 2022.01.16 ###
##############################

namespace app\routes;

use app\constants\Route;
use app\helpers\Authenticate;
use app\helpers\Util;

class UserEdit extends BaseRoute
{
    function __construct()
    {
        parent::__construct(Route::USER_EDIT, 'user_edit_template', 'user_edit_script');
    }

    public function getBodyContent(): string
    {
        if (!Authenticate::isLoggedIn()) {
            $this->requestRedirect(Route::LOGIN);
            return '';
        }

        if (!Authenticate::isAdmin()) {
            $this->requestRedirect(Route::HOME);
            return '';
        }

        return $this->renderTemplate(['password' => $password_plain ?? Util::getRandomPassword()]);
    }
}
