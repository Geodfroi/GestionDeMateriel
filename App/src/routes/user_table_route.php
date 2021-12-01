<?php

################################
## Joël Piguet - 2021.12.01 ###
##############################

namespace routes;

use helpers\Authenticate;
use helpers\Database;
use helpers\UserOrder;

/**
 * Route class containing behavior linked to admin_template. This route handles all admin related tasks.
 */
class UserTable extends BaseRoute
{
    function __construct()
    {
        parent::__construct(USER_TABLE_TEMPLATE, USERS_TABLE);
    }

    public function getBodyContent(): string
    {
        if (!Authenticate::isLoggedIn()) {
            $this->requestRedirect(LOGIN);
        }

        $_SESSION[USERS_PAGE] ??= 1;
        $_SESSION[USERS_ORDERBY] ??= UserOrder::EMAIL_ASC;

        if ($_SERVER['REQUEST_METHOD'] === 'GET') {

            if (isset($_GET['alert'])) {
                if ($_GET['alert'] === 'added_success') {
                    $this->setAlert(AlertType::SUCCESS, USER_ADD_SUCCESS);
                } else if ($_GET['alert'] === 'added_failure') {
                    $this->setAlert(AlertType::FAILURE, USER_ADD_FAILURE);
                }
            } else
            if (isset($_GET['orderby'])) {
                $_SESSION[USERS_ORDERBY] = intval($_GET['orderby']);
            } else if (isset($_GET['page'])) {
                $_SESSION[USERS_PAGE] = intval($_GET['page']);
            } else
            if (isset($_GET['delete'])) {
                $user_id = $_GET['delete'];
                $user = Database::getInstance()->getUserById($user_id);

                if (Database::getInstance()->deleteUserByID($user_id)) {
                    if (Database::getInstance()->deleteUserArticles($user_id)) {
                        $this->setAlert(AlertType::SUCCESS, USER_REMOVE_SUCCESS);
                    } else {
                        Database::getInstance()->insertUser($user); // insert back user if article delete was unsuccessful.
                        $this->setAlert(AlertType::FAILURE, USER_REMOVE_FAILURE);
                    }
                } else {
                    $this->setAlert(AlertType::FAILURE, USER_REMOVE_FAILURE);
                }
            } else  if (isset($_GET['connect'])) {
                Authenticate::login_as($_GET['connect']);
                $this->requestRedirect(ART_TABLE);
                return '';
            }
        }

        $item_count = Database::getInstance()->getUsersCount(false);
        $offset =   ($_SESSION[USERS_PAGE] - 1) * TABLE_DISPLAY_COUNT;
        $page_count = ceil($item_count / TABLE_DISPLAY_COUNT);
        $users = Database::getInstance()->getUsers(TABLE_DISPLAY_COUNT, $offset, $_SESSION[USERS_ORDERBY], false);

        return $this->renderTemplate([
            'users' =>  $users,
            'page_count' => $page_count,
            'page' => $_SESSION[USERS_PAGE],
            'orderby' => $_SESSION[USERS_ORDERBY],
        ]);
    }
}
