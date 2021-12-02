<?php

################################
## Joël Piguet - 2021.12.01 ###
##############################

namespace app\routes;

use app\helpers\Authenticate;

class Admin extends BaseRoute
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
