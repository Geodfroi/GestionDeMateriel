<?php

declare(strict_types=1);

################################
## Joël Piguet - 2022.04.05 ###
##############################

use app\constants\Alert;
use app\constants\AlertType;
use app\constants\LogInfo;
use app\constants\Route;
use app\helpers\Authenticate;
use app\helpers\BaseRoute;
use app\helpers\Database;
use app\helpers\Mailing;
use app\helpers\RequestUtil;
use app\helpers\Util;
use app\helpers\Validation;
use app\helpers\Logging;
use app\models\User;


require_once __DIR__ . '/../loader.php';
require_once __DIR__ . '/../vendor/autoload.php'; // use composer to load autofile.
session_start();


class UserEdit extends BaseRoute
{
    function __construct()
    {
        parent::__construct('useredit', 'useredit_template', 'useredit_script');
    }

    public function getBodyContent(): string
    {
        return $this->renderTemplate(['password' => $password_plain ?? Util::getRandomPassword()]);
    }
}

function addNewUser($json)
{
    $login_email = isset($json['login-email']) ? $json['login-email'] : "";
    $password_plain = isset($json['password']) ? $json['password'] : "";
    $is_admin = isset($json['is-admin']) ? $json['is-admin'] : "";

    $new_user = User::fromForm($login_email, $password_plain, $is_admin);
    $id = Database::users()->insert($new_user);
    if ($id) {
        if (Mailing::userInviteNotification($new_user, $password_plain)) {
            Logging::info(LogInfo::USER_CREATED, [
                'admin-id' => Authenticate::getUserId(),
                'new-user' => $login_email
            ]);
            Util::redirectTo(Route::USERS_TABLE, AlertType::SUCCESS, Alert::USER_ADD_SUCCESS);
            return;
        }
        //attempt to roll back adding new user to db.
        Database::users()->delete($id);
    }
    Util::redirectTo(Route::USERS_TABLE, AlertType::FAILURE, Alert::USER_ADD_FAILURE);
}

/**
 * @return string json response.
 */
function regenPassword(): string
{
    return json_encode(['password' => Util::getRandomPassword()]);
}

/**
 * @return string json response.
 */
function validateNewUser($json): string
{
    $login_email = isset($json['login-email']) ? $json['login-email'] : "";
    $password_plain = isset($json['password']) ? $json['password'] : "";

    $warnings = [];

    if ($login_warning = Validation::validateNewLogin($login_email)) {
        $warnings['login-email'] = $login_warning;
    }

    if ($password_warning  = Validation::validateNewPassword($password_plain)) {
        $warnings['password'] = $password_warning;
    }
    $json['warnings'] = $warnings;
    $json['validated'] = !$login_warning && !$password_warning;

    return json_encode($json);
}


Logging::debug("useredit route");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $json = RequestUtil::retrievePOSTData();
    Logging::debug("useredit POST request", $json);

    if (isset($json['add-user'])) {
        echo addNewUser($json);
    } else if (isset($json['validate-user'])) {
        echo validateNewUser($json);
    } else if (isset($json['regen-password'])) {
        echo regenPassword($json);
    }
} else {
    if (!Authenticate::isLoggedIn()) {
        Util::redirectTo(Route::LOGIN);
    } else if (!Authenticate::isAdmin()) {
        Util::redirectTo(Route::HOME);
    } else {
        echo (new UserEdit())->renderRoute();
    }
}
