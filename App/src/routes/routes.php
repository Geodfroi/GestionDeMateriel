<?php

################################
## Joël Piguet - 2021.11.23 ###
##############################

namespace routes;

use routes\AdminRoute;
use routes\ArticlesList;
use routes\BaseRoute;
use routes\ContactRoute;
use routes\Login;
use routes\ProfileRoute;

/**
 * Contains route const bundled into a class as well as getRoute() static function.
 */
class Routes
{
    const ADMIN = '/admin';
    const ARTICLES = '/articlesList';
    const ARTICLE_EDIT = '/articleEdit';
    const CONTACT = '/contact';
    const LOGIN = '/login';
    const LOGOUT = '/login?logout=true';
    const PROFILE = '/profile';

    /**
     * Get proper route from path contained in $_SERVER['PATH_INFO']; 
     * Each route defines its own page content to be inserted into the main template
     * 
     * @return BaseRoute an instance of a class inheriting BaseRoute.
     */
    public static function getRoute(): BaseRoute
    {
        switch ($_SERVER['PATH_INFO'] ?? '/') {
            case Routes::ADMIN:
                return new AdminRoute();
            case Routes::CONTACT:
                return new ContactRoute();
            case Routes::LOGIN:
                return new Login();
            case ROUTES::PROFILE:
                return new ProfileRoute();
            case Routes::ARTICLES:
            case '/':
                return new ArticlesList();
            case Routes::ARTICLE_EDIT:
                return new ArticleEdit();
            default:
                return new class extends BaseRoute
                {
                    public function __construct()
                    {
                        parent::__construct('');
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
