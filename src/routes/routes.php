<?php

################################
## Joël Piguet - 2021.12.01 ###
##############################

namespace app\routes;

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
        error_log("cccccccc");

        switch ($_SERVER['PATH_INFO'] ?? HOME) {
            case ADMIN:
                return new Admin();
            case ART_EDIT:
                return new ArticleEdit();
            case ART_TABLE:
                return new ArticleTable();
            case CONTACT:
                return new Contact();
            case HOME:
                return new Home();
            case LOCAL_PRESETS:
                return new LocationList();
            case LOGIN:
                return new Login();
            case PROFILE:
                return new Profile();
            case USER_EDIT:
                return new UserEdit();
            case USERS_TABLE:
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
