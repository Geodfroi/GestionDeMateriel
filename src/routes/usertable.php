<?php

################################
## Joël Piguet - 2022.01.30 ###
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

        $display_data = isset($_SESSION[Session::USERS_DISPLAY]) ? json_decode($_SESSION[Session::USERS_DISPLAY], true) : [];
        if (!isset($display_data['page'])) {
            $display_data['page'] = 1;
        }
        if (!isset($display_data['orderby'])) {
            $display_data['orderby'] = OrderBy::EMAIL_ASC;
        }

        if (isset($_GET['orderby'])) {
            $display_data['orderby'] = $_GET['orderby'];
        }

        if (isset($_GET['page'])) {
            $display_data['page'] = intval($_GET['page']);
        }

        $_SESSION[Session::USERS_DISPLAY] = json_encode($display_data);

        $display_count = TABLE_DISPLAY_COUNT;
        $item_count = Database::users()->queryCount(false);
        $offset =   ($display_data['page'] - 1) *      $display_count;
        $page_count = ceil($item_count /      $display_count);

        $users = Database::users()->queryAll($display_count, $offset, $display_data['orderby'], false);

        return $this->renderTemplate([
            'users' =>  $users,
            'display_data' => $display_data,
            'page_count' => $page_count,
        ]);
    }
}
