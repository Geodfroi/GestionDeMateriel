<?php

declare(strict_types=1);

################################
## Joël Piguet - 2022.03.14 ###
##############################

use app\constants\Route;
use app\helpers\Logging;
use app\helpers\Authenticate;
use app\helpers\Util;
use app\routes\LocationList;

require_once __DIR__ . '/../loader.php';
require_once __DIR__ . '/../vendor/autoload.php'; // use composer to load autofile.
session_start();

Logging::debug("locationpresets route");

if (!Authenticate::isLoggedIn()) {
    Util::requestRedirect(Route::LOGIN);
} else {
    echo (new LocationList())->renderRoute();
}
