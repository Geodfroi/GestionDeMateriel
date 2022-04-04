<?php

declare(strict_types=1);

################################
## Joël Piguet - 2022.04.04 ###
##############################

use app\helpers\Logging;
use app\helpers\Util;


require_once __DIR__ . '/../loader.php';
require_once __DIR__ . '/../vendor/autoload.php'; // use composer to load autofile.
session_start();

Logging::debug("debug page");
echo Util::renderTemplate('debug_template');
