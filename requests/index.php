<?php

declare(strict_types=1);

use app\helpers\RequestManager;

################################
## Joël Piguet - 2022.03.14 ###
##############################
/**
 * This route handle app fetch requests for data from javascript.
 */

require_once __DIR__ . '/../loader.php';
require_once __DIR__ . '/../vendor/autoload.php'; // use composer to load autofile.
session_start();

echo RequestManager::call();
