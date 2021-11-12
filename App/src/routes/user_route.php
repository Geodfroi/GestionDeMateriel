<?php

################################
## Joël Piguet - 2021.11.11 ###
##############################

namespace routes;

use helpers\Authenticate;

class UserRoute extends BaseRoute
{
    public function getPageContent(): string
    {
        if (!Authenticate::isLoggedIn()) {
            $this->requestRedirect('/login');
        }

        return "user route";
    }
}
