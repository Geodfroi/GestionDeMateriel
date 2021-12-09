<?php

################################
## Joël Piguet - 2021.12.01 ###
##############################

namespace app\routes;

use app\constants\Route;
use app\constants\Session;

use app\helpers\Authenticate;

class Admin extends BaseRoute
{
    function __construct()
    {
        parent::__construct('admin_template', Route::ADMIN);
    }

    public function getBodyContent(): string
    {
        if (!Authenticate::isLoggedIn()) {
            $this->requestRedirect(Route::LOGIN);
        }

        return $this->renderTemplate([
            'locations' => $locations ?? []
        ]);
    }
}
