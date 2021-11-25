<?php

################################
## Joël Piguet - 2021.11.25 ###
##############################

namespace routes;

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
        switch ($_SERVER['PATH_INFO'] ?? HOME) {
            case ADMIN:
                return new AdminRoute();
            case ART_EDIT:
                return new ArticleEdit();
            case ART_TABLE:
                return new ArticlesTable();
            case CONTACT:
                return new ContactRoute();
            case HOME:
                return new HomeRoute();
            case LOGIN:
                return new Login();
            case PROFILE:
                return new ProfileRoute();
            case USER_EDIT:
                return new UserEdit();
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
