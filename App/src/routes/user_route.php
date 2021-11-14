<?php

################################
## Joël Piguet - 2021.11.14 ###
##############################

namespace routes;

use helpers\Authenticate;
use helpers\Database;

const DEBUG_LIMIT = 6;
const DEBUG_OFFSET = 0;

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

        $user = Authenticate::getUser();
        $entries = Database::getInstance()->getEntries($user->getId(), DEBUG_LIMIT, DEBUG_OFFSET);

        // return render_template("user_template", ['form_errors' => $form_errors, 'email' => $email]);
        return $this->renderTemplate();
    }
}
