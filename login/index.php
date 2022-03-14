<?php

declare(strict_types=1);

use app\constants\Route;
use app\helpers\Authenticate;
use app\helpers\Util;
use app\helpers\Logging;
use app\routes\Login;

################################
## Joël Piguet - 2022.03.14 ###
##############################

require_once __DIR__ . '/../loader.php';
require_once __DIR__ . '/../vendor/autoload.php'; // use composer to load autofile.
session_start();

Logging::debug("login route");

if (Authenticate::isLoggedIn()) {
    return Util::requestRedirect(Route::HOME);
} else {
    echo (new Login())->renderRoute();
}
