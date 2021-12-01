<?php

################################
## Joël Piguet - 2021.12.01 ###
##############################

namespace routes;

use helpers\Authenticate;
use helpers\Database;
use helpers\Util;
use models\User;

class UserEdit extends BaseRoute
{
    function __construct()
    {
        parent::__construct(USER_EDIT_TEMPLATE, USER_EDIT);
    }

    public function getBodyContent(): string
    {
        if (!Authenticate::isLoggedIn()) {
            $this->requestRedirect(LOGIN);
            return '';
        }

        if (!Authenticate::isAdmin()) {
            $this->requestRedirect(HOME);
            return '';
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $is_admin = isset($_POST['is_admin']);

            if (isset($_POST['new-user'])) {
                $p_val = Util::validatePassword($this, $password);
                $e_val = $this->validate_email($email);

                if ($p_val && $e_val) {

                    $user = User::fromForm($email, $password, $is_admin);

                    if (Database::getInstance()->insertUser($user)) {
                        $this->requestRedirect(ADMIN . '?alert=added_success');
                    } else {
                        $this->requestRedirect(ADMIN . '?alert=added_failure');
                    }
                    return '';
                }
            } else if (isset($_POST['regen-password'])) {
                $password = $this->getRandomPassword();
                $this->validate_email($email);
            }
        }

        return $this->renderTemplate([
            'email' => $email ?? '',
            'password' => $password ?? $this->getRandomPassword(),
            'is-admin' => $is_admin  ?? false,
        ]);
    }

    /**
     * Generate a valid random password.
     */
    private function getRandomPassword()
    {
        $password_candidate = Util::randomString(DEFAULT_PASSWORD_LENGTH);
        $has_number = preg_match('@[0-9]@', $password_candidate);
        $has_letters = preg_match('@[a-zA-Z]@', $password_candidate);
        if ($has_number && $has_letters) {
            return $password_candidate;
        }
        return $this->getRandomPassword();
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
            $this->setError('email', USER_EMAIL_EMPTY);
            return false;
        }
        $email = filter_var($email, FILTER_VALIDATE_EMAIL);
        if (!$email) {
            $this->setError('email', USER_EMAIL_INVALID);
            return false;
        }
        if (Database::getInstance()->getUserByEmail($email)) {
            $this->setError('email', USER_EMAIL_USED);
            return false;
        }

        return true;
    }
}
