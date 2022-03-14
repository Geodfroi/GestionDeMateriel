<?php

################################
## Joël Piguet - 2022.01.17 ###
##############################

namespace app\routes;

use app\constants\Route;
use app\helpers\Authenticate;

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

        return $this->renderTemplate();
    }
}
