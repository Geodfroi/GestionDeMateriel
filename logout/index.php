<?php

declare(strict_types=1);

use app\constants\Route;
use app\helpers\Authenticate;
use app\helpers\Util;

################################
## Joël Piguet - 2022.04.08 ###
##############################

require_once __DIR__ . '/../loader.php';
require_once __DIR__ . '/../vendor/autoload.php'; // use composer to load autofile.
session_start();

Authenticate::logout();
Util::redirectTo(Route::LOGIN);
return "";
