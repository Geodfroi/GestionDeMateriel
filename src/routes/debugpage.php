<?php

################################
## Joël Piguet - 2021.12.14 ###
##############################

namespace app\routes;

use app\constants\Route;

/**
 * Route class containing test logic and TODO list
 */
class DebugPage extends BaseRoute
{
    function __construct()
    {
        parent::__construct('debug_template', Route::CONTACT);
    }

    public function getBodyContent(): string
    {
        return $this->renderTemplate();
    }
}
