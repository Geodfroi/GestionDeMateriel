<?php

declare(strict_types=1);

################################
## Joël Piguet - 2021.11.14 ###
##############################

namespace helpers;

use routes\Routes;
use routes\BaseRoute;
use routes\LoginRoute;
use routes\UserRoute;

/**
 * Get proper route from path contained in $_SERVER['PATH_INFO']; 
 * Each route defines its own page content to be inserted into the main template
 * 
 * @return BaseRoute an instance of a class inheriting BaseRoute.
 */
function getRoute(): BaseRoute
{
    switch ($_SERVER['PATH_INFO'] ?? '/') {
        case Routes::LOGIN:
            return new LoginRoute();
        case Routes::USER:
        case '/':
            return new UserRoute();
        default:
            return new class extends BaseRoute
            {
                public function handle(): string
                {
                    $this->requestRedirect('/');
                    return '';
                }
            };
    }

    // switch ($_SERVER['PATH_INFO'] ?? '/') {
    //     case '/signup':
    //         return new Signup();
    //     case '/contacts':
    //         return new Contacts();
    //     case '/profile':
    //         return new Profile();
    //     case '/login':
    //         return new Login();
    //     case '/logout':
    //         return new Logout();
    //     case '/':
    //         return new class extends Handler
    //         {
    //             public function handle(): string
    //             {
    //                 if (Auth::userIsAuthenticated()) {
    //                     $this->requestRedirect('/profile');
    //                 }
    //                 return (new Template('home'))->render();
    //             }
    //         };
    //     default:
    //         return new class extends Handler
    //         {
    //             public function handle(): string
    //             {
    //                 $this->requestRedirect('/');
    //                 return '';
    //             }
    //         };
    // }
}
