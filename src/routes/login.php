<?php

################################
## Joël Piguet - 2021.12.10 ###
##############################

namespace app\routes;

use app\constants\Alert;
use app\constants\AlertType;
use app\constants\Error;
use app\constants\Route;
use app\helpers\Database;
use app\helpers\Authenticate;
use app\helpers\Util;

/**
 * Route class containing behavior linked to login_template
 */
class Login extends BaseRoute
{
    public function __construct()
    {
        parent::__construct('login_template', Route::LOGIN);
    }

    public function getBodyContent(): string
    {
        if (Authenticate::isLoggedIn()) {

            if (isset($_GET['logout'])) {
                Authenticate::logout();
                $this->setAlert(AlertType::INFO, Alert::LOGIN_USER_DISC);
            } else {
                $this->requestRedirect(Route::HOME);
                return '';
            }
        }

        if (isset($_GET['old-email'])) {

            // handle demand for new password.
            $email  = $_GET['old-email'];
            $user = Database::users()->queryByEmail($email);

            if (isset($user)) {
                Util::renewPassword($this, $user);
            }

            goto end;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            if ($this->validateEmailInput($email)) {
                $user = Database::users()->queryByEmail($email);
            }

            if (!isset($user)) {
                $this->setError('email', Error::LOGIN_NOT_FOUND);
            } else {
                if ($this->validatePasswordInput($password)) {
                    if ($user->verifyPassword($password)) {
                        Authenticate::login($user);
                        $this->requestRedirect(Route::HOME);
                        return "";
                    } else {
                        $this->setError('password', Error::LOGIN_INVALID_PASSWORD);
                    }
                }
            }
        }

        end:
        return $this->renderTemplate([
            'email' => $email ?? '',
            'password' => $password ?? '',
        ]);
    }

    /**
     * Validate input and fill $errors array with proper email error text to be displayed if it fails.
     * 
     * @param string $email User email by reference.
     * @return bool True if properly filled-in.
     */
    private function validateEmailInput(&$email): bool
    {
        $email = trim($_POST['email']) ?? '';

        if ($email  === '') {
            $this->setError('email', Error::LOGIN_EMAIL_EMPTY);
            return false;
        }
        $email = filter_var($email, FILTER_VALIDATE_EMAIL);
        if (!$email) {
            $this->setError('email', Error::LOGIN_EMAIL_INVALID);
            return false;
        }
        return true;
    }

    /**
     * Validate input and fill $errors array with proper password error text to be displayed if it fails.
     * 
     * @param string $password User password by reference
     * @return bool True if properly filled;
     */
    private function validatePasswordInput(&$password)
    {
        $password = trim($_POST['password']) ?? '';
        if ($password === '') {
            $this->setError('password', Error::LOGIN_PASSWORD_EMPTY);
            return false;
        }
        return true;
    }
}
