<?php

declare(strict_types=1);

################################
## Joël Piguet - 2022.04.04 ###
##############################

use app\constants\Route;
use app\helpers\Authenticate;
use app\helpers\BaseRoute;
use app\helpers\Logging;
use app\helpers\Util;

require_once __DIR__ . '/../loader.php';
require_once __DIR__ . '/../vendor/autoload.php'; // use composer to load autofile.
session_start();

class Admin extends BaseRoute
{
    function __construct()
    {
        parent::__construct(Route::ADMIN, 'admin_template');
    }

    public function getBodyContent(): string
    {

        return $this->renderTemplate([
            'locations' => $locations ?? []
        ]);
    }
}


Logging::debug("admin route");
if (!Authenticate::isLoggedIn()) {
    Util::requestRedirect(Route::LOGIN);
} else {
    echo (new Admin())->renderRoute();
}
