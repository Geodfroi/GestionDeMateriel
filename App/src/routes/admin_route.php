<?php

################################
## Joël Piguet - 2021.11.24 ###
##############################

namespace routes;

use helpers\Authenticate;
use helpers\Database;
use helpers\UserOrder;


/**
 * Route class containing behavior linked to admin_template. This route handles all admin related tasks.
 */
class AdminRoute extends BaseRoute
{
    const DISPLAY_COUNT = 12;

    function __construct()
    {
        parent::__construct('admin_template', Routes::ADMIN);
    }

    public function getBodyContent(): string
    {
        if (!Authenticate::isLoggedIn()) {
            $this->requestRedirect(Routes::LOGIN);
        }

        $alerts = [];
        // $admin = Authenticate::getUser();
        $orderby = UserOrder::EMAIL_ASC;
        $page = 1;

        $item_count = Database::getInstance()->getUsersCount(true);
        $offset =   ($page - 1) * AdminRoute::DISPLAY_COUNT;
        $page_count = ceil($item_count / AdminRoute::DISPLAY_COUNT);

        $users = Database::getInstance()->getAllUsers(AdminRoute::DISPLAY_COUNT, $offset, $orderby, true);

        return $this->renderTemplate([
            'users' =>  $users,
            'alerts' => $alerts,
            'orderby' => $orderby,
            'page' => $page,
            'page_count' => $page_count,
        ]);
    }
}
