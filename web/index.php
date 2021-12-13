<?php

declare(strict_types=1);

################################
## Joël Piguet - 2021.12.13 ###
##############################
// The single entry point for the application inside the web folder. The code in this page is executed with each refresh.

use app\constants\Settings;
use app\helpers\Util;
use app\routes\Router;

require_once __DIR__ . '/../vendor/autoload.php'; // use composer to load autofile.

// initiate session allowing for data permanence in _SESSION array as long as the browser is open.
session_start();
$templateData = [];

// each route correspond to a path (i.e '/login') and is responsible to dynamically generate customized html content.
if ($route = Router::getRoute()) {
    if (!$route->isRedirecting()) {
        $templateData['page_title'] = $route->getHeaderTitle();
        $templateData['page_content'] = $route->getBodyContent();
        $templateData['show_header'] = $route->showHeader();
        $templateData['show_footer'] = $route->showFooter();
    }
}
// insert dynamically generated html content into the main template.
echo Util::renderTemplate('main_template', $templateData, Settings::TEMPLATES_PATH);
