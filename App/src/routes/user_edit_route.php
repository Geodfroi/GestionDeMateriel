<?php

################################
## Joël Piguet - 2021.11.25 ###
##############################

namespace routes;

use helpers\Authenticate;
use helpers\Database;
use helpers\Util;
use models\User;

class UserEdit extends BaseRoute
{
    const EMAIL_EMPTY = 'Un e-mail est nécessaire pour créer un utilisateur.';
    const EMAIL_INVALID = "Il ne s'agit pas d'une adresse e-mail valide.";
    const EMAIL_USED = "Cet adresse e-mail est déjà utilisée par un autre utilisateur.";
    const PASSWORD_EMPTY = 'Il vous faut fournir un mot de passe.';
    const PASSWORD_SHORT = 'Le mot de passe doit avoir au minimum %s caractères.';
    const PASSWORD_WEAK = 'Le mot de passe doit comporter des chiffres et des lettres.';

    function __construct()
    {
        parent::__construct('user_edit_template', USER_EDIT);
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

        $errors = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $is_admin = isset($_POST['is_admin']);

            if (isset($_POST['new-user'])) {
                $p_val = $this->validate_password($password, $errors);
                $e_val = $this->validate_email($email, $errors);

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
                $this->validate_email($email, $errors);
            }
        }

        return $this->renderTemplate([
            'values' => [
                'email' => $email ?? '',
                'password' => $password ?? $this->getRandomPassword(),
                'is-admin' => $is_admin  ?? false,
            ],
            'errors' => $errors,
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
     * @param Array[string] &$errors Error array passed by reference to be modified in-function.
     * @return bool True if properly filled-in.
     */
    private function validate_email(&$email, &$errors): bool
    {
        $email = trim($_POST['email']) ?? '';

        if ($email  === '') {
            $errors['email'] = UserEdit::EMAIL_EMPTY;
            return false;
        }
        $email = filter_var($email, FILTER_VALIDATE_EMAIL);
        if (!$email) {
            $errors['email'] = UserEdit::EMAIL_INVALID;
            return false;
        }
        if (Database::getInstance()->getUserByEmail($email)) {
            $errors['email'] = UserEdit::EMAIL_USED;
            return false;
        }

        return true;
    }

    /**
     * Validate input and fill $errors array with proper password error text to be displayed if it fails.
     * https://www.codexworld.com/how-to/validate-password-strength-in-php/
     * 
     * @param string $password_candidate Proposed user password by reference.
     * @param Array[string] &$errors Error array passed by reference to be modified in-function.
     * @return bool True if password is properly formatted;
     */
    private function validate_password(&$password_candidate, &$errors)
    {
        $password_candidate = trim($_POST['password']) ?? '';
        if ($password_candidate === '') {
            $errors['password'] = UserEdit::PASSWORD_EMPTY;
            return false;
        }
        if (strlen($password_candidate) < USER_PASSWORD_MIN_LENGTH) {
            $errors['password'] = sprintf(UserEdit::PASSWORD_SHORT, USER_PASSWORD_MIN_LENGTH);
            return false;
        }
        $has_number = preg_match('@[0-9]@', $password_candidate);
        $has_letters = preg_match('@[a-zA-Z]@', $password_candidate);
        if (!$has_number || (!$has_letters)) {
            $errors['password'] = UserEdit::PASSWORD_WEAK;
            return false;
        }
        return true;
    }
}
