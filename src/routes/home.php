<?php

################################
## Joël Piguet - 2021.11.24 ###
##############################

namespace app\routes;

use app\helpers\Authenticate;

/**
 * Route class used for redirection.
 */
class Home extends BaseRoute
{
    function __construct()
    {
        parent::__construct('', HOME);
    }

    public function getBodyContent(): string
    {
        if (Authenticate::isLoggedIn()) {
            if (Authenticate::isAdmin()) {
                $this->requestRedirect(ADMIN);
            } else {
                $this->requestRedirect(ART_TABLE);
            }
        } else {
            $this->requestRedirect(LOGIN);
        }
        return '';
    }
}
