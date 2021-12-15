<?php

################################
## Joël Piguet - 2021.12.15 ###
##############################

namespace app\routes;

use app\constants\LogChannel;
use app\constants\Route;
use app\helpers\Database;
use app\helpers\Logging;

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
        // Logging::debug('backup db...');
        // // backup db before running
        // Database::backup(LogChannel::TEST);

        return $this->renderTemplate();
    }
}
