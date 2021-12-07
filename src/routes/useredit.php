<?php

################################
## Joël Piguet - 2021.12.06 ###
##############################

namespace app\routes;

use app\constants\Alert;
use app\constants\Error;
use app\constants\Route;
use app\helpers\Authenticate;
use app\helpers\Database;
use app\helpers\Util;
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

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $is_admin = isset($_POST['is_admin']);

            if (isset($_POST['new-user'])) {
                $p_val = Util::validatePassword($this, $password);
                $e_val = $this->validate_email($email);

                if ($p_val && $e_val) {

                    $user = User::fromForm($email, $password, $is_admin);

                    if (Database::users()->insert($user)) {
                        $this->requestRedirect(Route::ADMIN . '?alert=added_success');
                    } else {
                        $this->requestRedirect(Route::ADMIN . '?alert=added_failure');
                    }
                    return '';
                }
            } else if (isset($_POST['regen-password'])) {
                $password = Util::getRandomPassword();
                $this->validate_email($email);
            }
        }

        return $this->renderTemplate([
            'email' => $email ?? '',
            'password' => $password ?? Util::getRandomPassword(),
            'is-admin' => $is_admin  ?? false,
        ]);
    }

    /**
     * Validate input and fill $errors array with proper email error text to be displayed if it fails.
     * 
     * @param string $email User email by reference.
     * @return bool True if properly filled-in.
     */
    private function validate_email(&$email): bool
    {
        $email = trim($_POST['email']) ?? '';

        if ($email  === '') {
            $this->setError('email', Error::USER_EMAIL_EMPTY);
            return false;
        }
        $email = filter_var($email, FILTER_VALIDATE_EMAIL);
        if (!$email) {
            $this->setError('email', Error::USER_EMAIL_INVALID);
            return false;
        }
        if (Database::users()->queryByEmail($email)) {
            $this->setError('email', Error::USER_EMAIL_USED);
            return false;
        }

        return true;
    }
}
