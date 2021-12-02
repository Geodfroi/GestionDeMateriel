<?php

################################
## Joël Piguet - 2021.12.02 ###
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

        if (isset($_GET['alert'])) {
            if ($_GET['alert'] === 'added_success') {
                $this->setAlert(AlertType::SUCCESS, USER_ADD_SUCCESS);
            } else if ($_GET['alert'] === 'added_failure') {
                $this->setAlert(AlertType::FAILURE, USER_ADD_FAILURE);
            }
            goto end;
        }

        if (isset($_GET['orderby'])) {
            $_SESSION[USERS_ORDERBY] = intval($_GET['orderby']);
            goto end;
        }

        if (isset($_GET['page'])) {
            $_SESSION[USERS_PAGE] = intval($_GET['page']);
            goto end;
        }

        if (isset($_GET['delete'])) {
            $user_id = $_GET['delete'];
            $user = Database::users()->queryById($user_id);

            if (Database::users()->delete($user_id)) {
                if (Database::articles()->deleteUserArticles($user_id)) {
                    $this->setAlert(AlertType::SUCCESS, USER_REMOVE_SUCCESS);
                } else {
                    Database::users()->insert($user); // insert back user if article delete was unsuccessful.
                    $this->setAlert(AlertType::FAILURE, USER_REMOVE_FAILURE);
                }
            } else {
                $this->setAlert(AlertType::FAILURE, USER_REMOVE_FAILURE);
            }
            goto end;
        }

        if (isset($_GET['connect'])) {
            Authenticate::login_as($_GET['connect']);
            $this->requestRedirect(ART_TABLE);
            return '';
        }

        end:

        $item_count = Database::users()->queryCount(false);
        $offset =   ($_SESSION[USERS_PAGE] - 1) * TABLE_DISPLAY_COUNT;
        $page_count = ceil($item_count / TABLE_DISPLAY_COUNT);
        $users = Database::users()->queryAll(TABLE_DISPLAY_COUNT, $offset, $_SESSION[USERS_ORDERBY], false);

        return $this->renderTemplate([
            'users' =>  $users,
            'page_count' => $page_count,
            'page' => $_SESSION[USERS_PAGE],
            'orderby' => $_SESSION[USERS_ORDERBY],
        ]);
    }
}