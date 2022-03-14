<?php

################################
## Joël Piguet - 2022.03.14 ###
##############################

namespace app\routes;

use app\constants\Route;

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
