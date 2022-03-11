<?php

################################
## Joël Piguet - 2021.11.24 ###
##############################

namespace app\routes;

use app\constants\Route;
use app\helpers\Authenticate;

/**
 * Route class used for redirection.
 */
class Home extends BaseRoute
{
    function __construct()
    {
        parent::__construct(Route::HOME, '');
    }

    public function getBodyContent(): string
    {
        if (Authenticate::isLoggedIn()) {
            if (Authenticate::isAdmin()) {
                $this->requestRedirect(Route::ADMIN);
            } else {
                $this->requestRedirect(Route::ART_TABLE);
            }
        } else {
            $this->requestRedirect(Route::LOGIN);
        }
        return '';
    }
}
