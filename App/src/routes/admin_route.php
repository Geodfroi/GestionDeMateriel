<?php

################################
## Joël Piguet - 2021.11.24 ###
##############################

namespace routes;

use helpers\Authenticate;
use helpers\Database;


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

        if ($_SERVER['REQUEST_METHOD'] === 'GET') {

            if (isset($_GET['orderby'])) {
                $_SESSION[ADMIN_ORDER_BY] = intval($_GET['orderby']);
            } else if (isset($_GET['page'])) {
                $_SESSION[ADMIN_PAGE] = intval($_GET['page']);
            }

            // if (isset($_GET['alert'])) {
            //     if ($_GET['alert'] === 'added_success') {
            //         $alerts['success'] = ArticlesTable::ADD_SUCCESS;
            //     } else if ($_GET['alert'] === 'added_failure') {
            //         $alerts['failure'] = ArticlesTable::ADD_FAILURE;
            //     } else if ($_GET['alert'] === 'updated_success') {
            //         $alerts['success'] = ArticlesTable::UPDATE_SUCCESS;
            //     } else if ($_GET['alert'] === 'updated_failure') {
            //         $alerts['failure'] = ArticlesTable::UPDATE_FAILURE;
            //     }
            //     if (isset($_GET['delete'])) {
            //         if (Database::getInstance()->deleteArticleByID($_GET['delete'])) {
            //             $alerts['success'] = ArticlesTable::REMOVE_SUCCESS;
            //         } else {
            //             $alerts['failure'] = ArticlesTable::REMOVE_FAILURE;
            //         }
            //     }
        }


        $item_count = Database::getInstance()->getUsersCount(true);
        $offset =   ($_SESSION[ADMIN_PAGE] - 1) * AdminRoute::DISPLAY_COUNT;
        $page_count = ceil($item_count / AdminRoute::DISPLAY_COUNT);

        $users = Database::getInstance()->getUsers(AdminRoute::DISPLAY_COUNT, $offset, $_SESSION[ADMIN_ORDER_BY], true);

        return $this->renderTemplate([
            'users' =>  $users,
            'alerts' => $alerts,
            'page_count' => $page_count,
            'page' => $_SESSION[ADMIN_PAGE],
            'orderby' => $_SESSION[ADMIN_ORDER_BY],
        ]);
    }
}
