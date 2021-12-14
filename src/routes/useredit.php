<?php

################################
## Joël Piguet - 2021.12.14 ###
##############################

namespace app\routes;

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
        parent::__construct('user_edit_template', Route::USER_EDIT);
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
            $p_val = Validation::validateNewPassword($this, $password_plain);
            $e_val = Validation::validateNewLogin($this, $login_email);
            $is_admin = isset($_POST['is-admin']);

            if ($p_val && $e_val) {
                $user = User::fromForm($login_email, $password_plain, $is_admin);

                $id = Database::users()->insert($user);
                if ($id) {
                    if (Mailing::userInviteNotification($user, $password_plain)) {
                        Logging::info(LogInfo::USER_CREATED, [
                            'admin-id' => $admin_id,
                            'new-user' => $login_email
                        ]);
                        $this->requestRedirect(Route::USERS_TABLE . '?alert=added_success');
                        return '';
                    } else {
                        //attempt to roll back add user to db.
                        Database::users()->delete($id);
                    }
                }
                $this->requestRedirect(Route::USERS_TABLE . '?alert=added_failure');
                return '';
            }
            goto end;
        }

        if (isset($_POST['regen-password'])) {
            $is_admin = isset($_POST['is_admin']);
            $password_plain = Util::getRandomPassword();
            Validation::validateNewLogin($this, $login_email);
            goto end;
        }

        end:

        return $this->renderTemplate([
            'login_email' => $login_email ?? '',
            'password' => $password_plain ?? Util::getRandomPassword(),
            'is_admin' => $is_admin ?? false,
        ]);
    }
}
