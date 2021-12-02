<?php

declare(strict_types=1);

################################
## Joël Piguet - 2021.12.02 ###
##############################
// The single entry point for the application inside the web folder. The code in this page is executed with each refresh 

use app\helpers\Util;
use app\routes\Routes;

require_once __DIR__ . '/../src/const.php';
require_once __DIR__ . '/../db_settings.php';
require_once __DIR__ . '/../vendor/autoload.php'; // use composer to load autofile.

// initiate session allowing for data permanence in _SESSION array as long as the browser is open.
session_start();

$templateData = [];

// each route correspond to a path (i.e '/login') and is responsible to dynamically generate customized html content.
if ($route = Routes::getRoute()) {
    if (!$route->isRedirecting()) {
        $templateData['page_title'] = $route->getHeaderTitle();
        $templateData['page_content'] = $route->getBodyContent();
    }
}


// insert dynamically generated html content into the main template.
echo Util::renderTemplate('main_template', $templateData);
