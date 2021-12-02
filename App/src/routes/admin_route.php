<?php

################################
## Joël Piguet - 2021.12.01 ###
##############################

namespace routes;

use helpers\Authenticate;
use helpers\Database;

class AdminRoute extends BaseRoute
{
    function __construct()
    {
        parent::__construct(ADMIN_TEMPLATE, ADMIN);
    }

    public function getBodyContent(): string
    {
        if (!Authenticate::isLoggedIn()) {
            $this->requestRedirect(LOGIN);
        }

        return $this->renderTemplate([
            'locations' => $locations ?? []
        ]);
    }
}
