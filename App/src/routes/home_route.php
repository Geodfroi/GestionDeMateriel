<?php

################################
## Joël Piguet - 2021.11.24 ###
##############################

namespace routes;

use helpers\Authenticate;

/**
 * Route class used for redirection.
 */
class HomeRoute extends BaseRoute
{
    function __construct()
    {
        parent::__construct('', Routes::HOME);
    }

    public function getBodyContent(): string
    {
        if (Authenticate::isLoggedIn()) {
            if (Authenticate::isAdmin()) {
                $this->requestRedirect(Routes::ADMIN);
            } else {
                $this->requestRedirect(Routes::ART_TABLE);
            }
        } else {
            $this->requestRedirect(Routes::LOGIN);
        }
        return '';
    }
}
