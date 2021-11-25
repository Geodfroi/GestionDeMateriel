<?php

################################
## Joël Piguet - 2021.11.25 ###
##############################

namespace routes;

use helpers\Authenticate;
use helpers\Database;
use helpers\Util;

class UserEdit extends BaseRoute
{
    const EMAIL_EMPTY = 'Un e-mail est nécessaire pour créer un utilisateur.';
    const EMAIL_INVALID = "Il ne s'agit pas d'une adresse e-mail valide.";
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

            if ($this->validate_email($email, $errors)) {
                if ($this->validate_password($password, $errors)) {
                    $this->requestRedirect(ADMIN . '?alert=added_success');
                    return '';
                }
            }
        }

        $values = [
            'id' => $user_id ?? 'no-id',
            'email' => $email ?? '',
            'password' => $password ?? Util::randomString(DEFAULT_PASSWORD_LENGTH),
        ];

        return $this->renderTemplate([
            'values' => $values,
            'errors' => $errors,
        ]);
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
