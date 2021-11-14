<?php

################################
## Joël Piguet - 2021.11.14 ###
##############################

namespace routes;

use helpers\Authenticate;

const USER_TEMPLATE = "user_template";

class UserRoute extends BaseRoute
{
    public function __construct()
    {
        parent::__construct(USER_TEMPLATE);
    }

    public function getBodyContent(): string
    {
        if (!Authenticate::isLoggedIn()) {
            $this->requestRedirect(Routes::LOGIN);
        }

        // return render_template("user_template", ['form_errors' => $form_errors, 'email' => $email]);
        return $this->renderTemplate();
    }
}
