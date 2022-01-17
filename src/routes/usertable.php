<?php

################################
## Joël Piguet - 2022.01.16 ###
##############################

namespace app\routes;

use app\constants\OrderBy;
use app\constants\Route;
use app\constants\Session;
use app\constants\Settings;
use app\helpers\Authenticate;
use app\helpers\Database;

/**
 * Route class containing behavior linked to admin_template. This route handles all admin related tasks.
 */
class UserTable extends BaseRoute
{
    function __construct()
    {
        parent::__construct(Route::USERS_TABLE, 'user_table_template', 'user_table_script');
    }

    public function getBodyContent(): string
    {
        if (!Authenticate::isLoggedIn()) {
            $this->requestRedirect(Route::LOGIN);
        }

        $_SESSION[Session::USERS_PAGE] ??= 1;
        $_SESSION[Session::USERS_ORDERBY] ??= OrderBy::EMAIL_ASC;

        if (isset($_GET['orderby'])) {
            $_SESSION[Session::USERS_ORDERBY] = intval($_GET['orderby']);
        }

        if (isset($_GET['page'])) {
            $_SESSION[Session::USERS_PAGE] = intval($_GET['page']);
        }

        $item_count = Database::users()->queryCount(false);
        $offset =   ($_SESSION[Session::USERS_PAGE] - 1) * Settings::TABLE_DISPLAY_COUNT;
        $page_count = ceil($item_count / Settings::TABLE_DISPLAY_COUNT);
        $users = Database::users()->queryAll(Settings::TABLE_DISPLAY_COUNT, $offset, $_SESSION[Session::USERS_ORDERBY], false);

        return $this->renderTemplate([
            'users' =>  $users,
            'page_count' => $page_count,
        ]);
    }
}
