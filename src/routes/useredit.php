<?php

################################
## Joël Piguet - 2022.03.14 ###
##############################

namespace app\routes;

use app\constants\Route;
use app\helpers\BaseRoute;
use app\helpers\Util;

class UserEdit extends BaseRoute
{
    function __construct()
    {
        parent::__construct(Route::USER_EDIT, 'user_edit_template', 'user_edit_script');
    }

    public function getBodyContent(): string
    {
        return $this->renderTemplate(['password' => $password_plain ?? Util::getRandomPassword()]);
    }
}
