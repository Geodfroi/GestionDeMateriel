<?php

declare(strict_types=1);

################################
## Joël Piguet - 2022.04.05 ###
##############################

use app\constants\OrderBy;
use app\constants\Route;
use app\constants\Session;
use app\helpers\Authenticate;
use app\helpers\BaseRoute;
use app\helpers\Database;
use app\helpers\Logging;
use app\helpers\Util;
use app\constants\Alert;
use app\constants\AlertType;
use app\constants\LogInfo;

require_once __DIR__ . '/../loader.php';
require_once __DIR__ . '/../vendor/autoload.php'; // use composer to load autofile.
session_start();

/**
 * Route class containing behavior linked to admin_template. This route handles all admin related tasks.
 */
class UserTable extends BaseRoute
{
    function __construct()
    {
        parent::__construct('usertable', 'usertable_template', 'usertable_script');
    }

    public function getBodyContent(): string
    {
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

function deleteUser($id)
{
    if (Database::users()->delete($id)) {

        Logging::info(LogInfo::USER_DELETED, [
            'admin-id' => Authenticate::getUserId(),
            'user-id' => $id
        ]);
        Util::requestRedirect(Route::USERS_TABLE, AlertType::SUCCESS, Alert::USER_REMOVE_SUCCESS);
    } else {
        Util::requestRedirect(Route::USERS_TABLE, AlertType::FAILURE, Alert::USER_REMOVE_FAILURE);
    }
}

/**
 * Called by admin on user-table to re-issue user a new password.
 */
function renewUserPassword($id)
{
    $user = Database::users()->queryById(intval($id));
    if (Util::renewPassword($user)) {
        Util::requestRedirect(Route::USERS_TABLE, AlertType::SUCCESS, sprintf(Alert::NEW_PASSWORD_SUCCESS, $user->getLoginEmail()));
    } else {
        Util::requestRedirect(Route::USERS_TABLE, AlertType::FAILURE, Alert::NEW_PASSWORD_FAILURE);
    }
}

Logging::debug("usertable route");
if (!Authenticate::isLoggedIn()) {
    Util::requestRedirect(Route::LOGIN);
} else if (!Authenticate::isAdmin()) {
    Util::requestRedirect(Route::HOME);
} else {
    if (isset($_GET['deleteuser'])) {
        $id_ = intval($_GET['deleteuser']);
        deleteUser($id_);
    } else if (isset($_GET['renewuserpassword'])) {
        $id_ = intval($_GET['renewuserpassword']);
        renewUserPassword($id_);
    } else {
        echo (new UserTable())->renderRoute();
    }
}
