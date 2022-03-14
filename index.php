<?php

declare(strict_types=1);

################################
## Joël Piguet - 2022.03.11 ###
##############################
// The single entry point for the application inside the web folder. The code in this page is executed with each refresh.

use app\helpers\RequestManager;

use app\routes\Router;

require_once __DIR__ . '/loader.php';
require_once __DIR__ . '/vendor/autoload.php'; // use composer to load autofile.

// initiate session allowing for data permanence in _SESSION array as long as the browser is open.
session_start();


// $templateData = [];

echo $_SERVER['PATH_INFO'];

if (isset($_SERVER['PATH_INFO']) && $_SERVER['PATH_INFO'] === '/request') {
    echo RequestManager::call();
} else {
    echo Router::renderRoute();
}
