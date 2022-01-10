<?php

################################
## Joël Piguet - 2022.01.10 ###
##############################

namespace app\routes;

use app\constants\Alert;
use app\constants\AlertType;
use app\constants\LogInfo;
use app\constants\OrderBy;
use app\constants\Route;
use app\constants\Session;
use app\constants\Settings;
use app\helpers\Authenticate;
use app\helpers\Database;
use app\helpers\Logging;
use app\helpers\Util;

/**
 * Route class containing behavior linked to admin_template. This route handles all admin related tasks.
 */
class UserTable extends BaseRoute
{
    function __construct()
    {
        parent::__construct(Route::USERS_TABLE, 'user_table_template', 'user_table');
    }

    public function getBodyContent(): string
    {
        if (!Authenticate::isLoggedIn()) {
            $this->requestRedirect(Route::LOGIN);
        }

        if (isset($_GET['delete'])) {
            $user_id = $_GET['delete'];

            if (Database::users()->delete($user_id)) {

                Logging::info(LogInfo::USER_DELETED, [
                    'admin-id' => Authenticate::getUserId(),
                    'user-id' => $user_id
                ]);
                return $this->requestRedirect(Route::USERS_TABLE, AlertType::SUCCESS, Alert::USER_REMOVE_SUCCESS);
            }
            return $this->requestRedirect(Route::USERS_TABLE, AlertType::FAILURE, Alert::USER_REMOVE_FAILURE);
        }

        if (isset($_GET['renew'])) {
            $user = Database::users()->queryById(intval($_GET['renew']));
            if (Util::renewPassword($user)) {
                return $this->requestRedirect(Route::USERS_TABLE, AlertType::SUCCESS, printf(Alert::NEW_PASSWORD_SUCCESS, $user->getLoginEmail()));
            }
            return $this->requestRedirect(Route::USERS_TABLE, AlertType::FAILURE, Alert::NEW_PASSWORD_FAILURE);
        }

        $_SESSION[Session::USERS_PAGE] ??= 1;
        $_SESSION[Session::USERS_ORDERBY] ??= OrderBy::EMAIL_ASC;

        if (isset($_GET['orderby'])) {
            $_SESSION[Session::USERS_ORDERBY] = intval($_GET['orderby']);
            goto end;
        }

        if (isset($_GET['page'])) {
            $_SESSION[Session::USERS_PAGE] = intval($_GET['page']);
        }

        end:

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
