<?php

################################
## Joël Piguet - 2022.01.13 ###
##############################

namespace app\routes;

use app\constants\AlertType;
use app\constants\Alert;
use app\constants\LogInfo;
use app\constants\Route;
use app\helpers\Authenticate;
use app\helpers\Database;
use app\helpers\Logging;
use app\helpers\Mailing;
use app\helpers\Util;
use app\helpers\Validation;
use app\models\User;

class UserEdit extends BaseRoute
{
    function __construct()
    {
        parent::__construct(Route::USER_EDIT, 'user_edit_template', 'user_edit_script');
    }

    public function getBodyContent(): string
    {
        if (!Authenticate::isLoggedIn()) {
            $this->requestRedirect(Route::LOGIN);
            return '';
        }

        if (!Authenticate::isAdmin()) {
            $this->requestRedirect(Route::HOME);
            return '';
        }

        $admin_id = Authenticate::getUserId();

        if (isset($_POST['new-user'])) {
            $login_email = trim($_POST['login-email']) ?? '';
            $password_plain = trim($_POST['password']) ?? '';

            $password_warning = Validation::validateNewPassword($password_plain);
            if ($password_warning) {
                $this->showWarning('password', $password_warning);
            }

            $login_warning = Validation::validateNewLogin($login_email);
            if ($login_warning) {
                $this->showWarning('login-email', $login_warning);
            }

            $is_admin = isset($_POST['is-admin']);

            if (!$password_warning && !$login_warning) {
                $user = User::fromForm($login_email, $password_plain, $is_admin);

                $id = Database::users()->insert($user);
                if ($id) {
                    if (Mailing::userInviteNotification($user, $password_plain)) {
                        Logging::info(LogInfo::USER_CREATED, [
                            'admin-id' => $admin_id,
                            'new-user' => $login_email
                        ]);
                        return $this->requestRedirect(Route::USERS_TABLE, AlertType::SUCCESS, Alert::USER_ADD_SUCCESS);
                    } else {
                        //attempt to roll back add user to db.
                        Database::users()->delete($id);
                    }
                }
                return $this->requestRedirect(Route::USERS_TABLE, AlertType::FAILURE, Alert::USER_ADD_FAILURE);
            }
        }

        return $this->renderTemplate([
            'login_email' => $login_email ?? '',
            'password' => $password_plain ?? Util::getRandomPassword(),
            'is_admin' => $is_admin ?? false,
        ]);
    }
}
