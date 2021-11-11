<?php

################################
## Joël Piguet - 2021.11.11 ###
##############################

namespace routes;

use routes\BaseRoute;
use function helpers\render_template;

class DefaultRoute extends BaseRoute
{
    public function  getPageContent(): string
    {
        // if (Auth::userIsAuthenticated()) {
        //     $this->requestRedirect('/profile');
        // }

        return render_template('home_template');
    }
}
