<?php

declare(strict_types=1);

################################
## Joël Piguet - 2022.03.14 ###
##############################

use app\helpers\Logging;
use app\routes\DebugEmails;

require_once __DIR__ . '/../loader.php';
require_once __DIR__ . '/../vendor/autoload.php'; // use composer to load autofile.
session_start();

Logging::debug("debugmails route");
echo (new DebugEmails())->renderRoute();
