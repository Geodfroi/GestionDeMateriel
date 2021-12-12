<?php

################################
## Joël Piguet - 2021.12.12 ###
##############################

namespace app\routes;

use app\constants\Route;
use app\constants\LogInfo;
use app\helpers\Authenticate;
use app\helpers\Logging;

/**
 * Contains route const bundled into a class as well as getRoute() static function.
 */
class Routes
{
    /**
     * Get proper route from path contained in $_SERVER['PATH_INFO']; 
     * Each route defines its own page content to be inserted into the main template
     * 
     * @return BaseRoute an instance of a class inheriting BaseRoute.
     */
    public static function getRoute(): BaseRoute
    {
        $route = $_SERVER['PATH_INFO'] ?? Route::HOME;

        //logging route if logged-in.
        if (Authenticate::isLoggedIn()) {
            Logging::info(LogInfo::ROUTING, ['route' => $route, 'user-id' => Authenticate::getUserId()]);
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
}
