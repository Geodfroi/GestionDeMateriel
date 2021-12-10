<?php

################################
## Joël Piguet - 2021.12.10 ###
##############################

namespace app\routes;

use app\constants\Route;

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

        // if ($route != Route::PROFILE) {
        //     //reset profile id when leaving profile page.
        //     $_SESSION[SESSION::PROFILE_ID] = Authenticate::getUserId();
        // }

        switch ($route) {
            case Route::ADMIN:
                return new Admin();
            case Route::ART_EDIT:
                return new ArticleEdit();
            case Route::ART_TABLE:
                return new ArticleTable();
            case Route::CONTACT:
                return new Contact();
            case Route::HOME:
                return new Home();
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
            default:
                return new class extends BaseRoute
                {
                    public function __construct()
                    {
                        parent::__construct('', '');
                    }

                    public function getBodyContent(): string
                    {
                        $this->requestRedirect('/');
                        return '';
                    }
                };
        }
    }
}
