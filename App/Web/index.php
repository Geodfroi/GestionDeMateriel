<?php

declare(strict_types=1);

################################
## Joël Piguet - 2021.11.11 ###
##############################
// The single entry point for the application inside the web folder. With each refresh the page

use function helpers\render_template;
use function helpers\getRoute;

require_once __DIR__ . '/../boot.php';

session_start();
$templateData = [];

if ($route = getRoute()) {
    if (!$route->isRedirecting()) {
        $templateData['page_title'] = $route->getPageTitle();
        $templateData['page_content'] = $route->getPageContent();
    }
}

echo render_template('main_template', $templateData);
