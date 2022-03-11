<?php

################################
## Joël Piguet - 2022.03.10 ###
##############################

namespace app\routes;

use app\constants\AppPaths;
use app\constants\Route;
use app\constants\LogInfo;
use app\helpers\Authenticate;
use app\helpers\Logging;
use app\helpers\Util;

/**
 * Contains route const bundled into a class as well as getRoute() static function.
 */
class Router
{
    /**
     * Get proper route from path contained in $_SERVER['PATH_INFO']; 
     * Each route defines its own page content to be inserted into the main template
     * 
     * @return BaseRoute an instance of a class inheriting BaseRoute.
     */
    private static function getRoute(): BaseRoute
    {
        $route = $_SERVER['PATH_INFO'] ?? Route::HOME;

        //logging route if logged-in and in debug mode.
        if (DEBUG_MODE && Authenticate::isLoggedIn()) {
            Logging::debug(LogInfo::ROUTING, [
                'route' => $route,
                'user-id' => Authenticate::getUserId()
            ]);
        }

        switch ($route) {
            case Route::ADMIN:
                return new Admin();
            case Route::ART_EDIT:
                return new ArticleEdit();
            case Route::ART_TABLE:
                return new ArticleTable();
            case Route::CONTACT:
                return new Contact();
            case Route::DEBUG_EMAILS:
                return new DebugEmails();
            case Route::DEBUG_PAGE:
                return new DebugPage();
            case Route::LOCAL_PRESETS:
                return new LocationList();
            case Route::LOGIN:
                return new Login();
            case Route::PROFILE:
                return new Profile();
            case Route::USER_EDIT:
                return new UserEdit();
            case Route::USERS_TABLE:
                return new UserTable();
            case Route::HOME:
            default:
                return new Home();
        }
    }

    /**
     * Each route correspond to a path (i.e '/login') and is responsible to dynamically generate customized html content.
     */
    public static function renderRoute(): String
    {
        if ($route = Router::getRoute()) {
            if (!$route->isRedirecting()) {
                $templateData['page_title'] = $route->getHeaderTitle();
                $templateData['page_content'] = $route->getBodyContent();
                $templateData['page_script'] = $route->getScript();
                $templateData['show_header'] = $route->showHeader();
                $templateData['show_footer'] = $route->showFooter();
                $templateData['alert'] = Util::displayAlert();
                $templateData['json_data'] = $route->getJSONData();
            }
        }
        // insert dynamically generated html content into the main template.
        return Util::renderTemplate('main_template', $templateData, AppPaths::TEMPLATES);
    }
}
