<?php

declare(strict_types=1);

################################
## Joël Piguet - 2022.03.14 ###
##############################

use app\constants\Route;
use app\helpers\Authenticate;
use app\helpers\Logging;
use app\helpers\Util;
use app\routes\UserTable;

require_once __DIR__ . '/../loader.php';
require_once __DIR__ . '/../vendor/autoload.php'; // use composer to load autofile.
session_start();

Logging::debug("usertable route");

if (!Authenticate::isLoggedIn()) {
    Util::requestRedirect(Route::LOGIN);
} else if (!Authenticate::isAdmin()) {
    Util::requestRedirect(Route::HOME);
} else {
    echo (new UserTable())->renderRoute();
}
