<?php

declare(strict_types=1);

################################
## Joël Piguet - 2022.04.05 ###
##############################

use app\constants\Route;
use app\constants\Session;
use app\helpers\Logging;
use app\helpers\Util;
use app\helpers\Authenticate;


require_once __DIR__ . '/vendor/autoload.php'; // use composer to load autofile.
// initiate session allowing for data permanence in _SESSION array as long as the browser is open.
require_once __DIR__ . '/loader.php';

session_start();
$_SESSION[Session::ROOT] = APP_URL;

Logging::debug("root");

if (Authenticate::isLoggedIn()) {
    Logging::debug("logged in");
    if (Authenticate::isAdmin()) {
        Logging::debug("is admin");
        Util::redirectTo(Route::ADMIN);
    } else {
        Util::redirectTo(Route::ART_TABLE);
    }
} else {
    Logging::debug("redirect: login");
    Util::redirectTo(Route::LOGIN);
}
