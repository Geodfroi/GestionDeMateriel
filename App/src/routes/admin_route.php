<?php

################################
## Joël Piguet - 2021.11.25 ###
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
        parent::__construct('admin_template', ADMIN);
    }

    public function getBodyContent(): string
    {
        if (!Authenticate::isLoggedIn()) {
            $this->requestRedirect(LOGIN);
        }

        $_SESSION[ADMIN_PAGE] ??= 1;
        $_SESSION[ADMIN_ORDER_BY] ??= UserOrder::EMAIL_ASC;

        $alerts = [];

        if ($_SERVER['REQUEST_METHOD'] === 'GET') {

            if (isset($_GET['alert'])) {
                if ($_GET['alert'] === 'added_success') {
                    $alerts['success'] = ArticlesTable::ADD_SUCCESS;
                } else if ($_GET['alert'] === 'added_failure') {
                    $alerts['failure'] = ArticlesTable::ADD_FAILURE;
                }
            } else
            if (isset($_GET['orderby'])) {
                $_SESSION[ADMIN_ORDER_BY] = intval($_GET['orderby']);
            } else if (isset($_GET['page'])) {
                $_SESSION[ADMIN_PAGE] = intval($_GET['page']);
            } else
            if (isset($_GET['delete'])) {
                $user_id = $_GET['delete'];

                $user = Database::getInstance()->getUserById($user_id);

                if (Database::getInstance()->deleteUserByID($user_id)) {
                    if (Database::getInstance()->deleteUserArticles($user_id)) {
                        $alerts['success'] = ArticlesTable::REMOVE_SUCCESS;
                    } else {
                        Database::getInstance()->insertUser($user); // insert back user if article delete was unsuccessful.
                        $alerts['failure'] = ArticlesTable::REMOVE_FAILURE;
                    }
                } else {
                    $alerts['failure'] = ArticlesTable::REMOVE_FAILURE;
                }
            } else  if (isset($_GET['connect'])) {
                Authenticate::login_as($_GET['connect']);
                $this->requestRedirect(ART_TABLE);
                return '';
            }
        }

        $item_count = Database::getInstance()->getUsersCount(false);
        $offset =   ($_SESSION[ADMIN_PAGE] - 1) * AdminRoute::DISPLAY_COUNT;
        $page_count = ceil($item_count / AdminRoute::DISPLAY_COUNT);
        $users = Database::getInstance()->getUsers(AdminRoute::DISPLAY_COUNT, $offset, $_SESSION[ADMIN_ORDER_BY], false);

        return $this->renderTemplate([
            'users' =>  $users,
            'alerts' => $alerts,
            'page_count' => $page_count,
            'page' => $_SESSION[ADMIN_PAGE],
            'orderby' => $_SESSION[ADMIN_ORDER_BY],
        ]);
    }
}
