<?php

################################
## Joël Piguet - 2021.11.17 ###
##############################

namespace routes;

use helpers\Database;
use helpers\Authenticate;
use routes\Routes;

/**
 * Bundled constants for Login
 */
class Login
{
    const EMAIL_KEY = 'email';
    const PASSWORD_KEY = 'password';

    const GET_OLD_EMAIL = 'old-email';

    const NEW_PASSWORD_ALERT = 'new-password';
    const DISCONNECT_ALERT = 'disconnect';

    const EMAIL_EMPTY = 'Un e-mail est nécessaire pour vous connecter.';
    const PASSWORD_EMPTY = 'Il vous faut fournir un mot de passe.';
    const EMAIL_INVALID = "Il ne s'agit pas d'une adresse e-mail valide.";
    const USER_NOT_FOUND = "Il n'existe pas d'usager employant cette adresse e-mail.";
    const PASSWORD_INVALID = "Le mot de passe n'est pas correct.";
}

/**
 * Route class containing behavior linked to login_template
 */
class LoginRoute extends BaseRoute
{
    public function __construct()
    {
        parent::__construct('login_template');
    }


    public function getBodyContent(): string
    {
        $values = [
            Login::EMAIL_KEY => '',
            Login::PASSWORD_KEY => '',
        ];
        $errors = [];
        $alerts = [];
        $user = '';

        if (Authenticate::isLoggedIn()) {

            if (isset($_GET['logout'])) {
                Authenticate::logout();
                $alerts['logout'] = '';
            } else {
                $this->requestRedirect(Routes::ARTICLES);
                return '';
            }
        }

        if (isset($_GET[Login::GET_OLD_EMAIL])) {
            // handle demand for new password.
            $values[Login::EMAIL_KEY]  = $_GET[Login::GET_OLD_EMAIL];
            $user = Database::getInstance()->getUserByEmail($values[Login::EMAIL_KEY]);

            if (isset($user)) {
                $this->handleNewPasswordRequest($user->getEmail());
                $alerts[LOGIN::NEW_PASSWORD_ALERT] = '';
            }
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            // handle login post request.
            $values[Login::EMAIL_KEY] = trim($_POST[Login::EMAIL_KEY]) ?? '';
            $values[Login::PASSWORD_KEY] = trim($_POST[Login::PASSWORD_KEY]) ?? '';

            if ($this->validate_email_input($values[Login::EMAIL_KEY], $errors)) {
                $user = Database::getInstance()->getUserByEmail($values[Login::EMAIL_KEY]);
            }

            if (!isset($user)) {
                $errors[Login::EMAIL_KEY] = Login::USER_NOT_FOUND;
            } else {
                if ($this->validate_password_input($values[Login::PASSWORD_KEY], $errors)) {
                    if ($user->verifyPassword($values[Login::PASSWORD_KEY])) {
                        Authenticate::login($user);
                        $this->requestRedirect(Routes::ARTICLES);
                        return "";
                    } else {
                        $errors[Login::PASSWORD_KEY] = Login::PASSWORD_INVALID;
                    }
                }
            }
        }

        return $this->renderTemplate([
            'values' => $values,
            'errors' => $errors,
            'alerts' => $alerts,
        ]);
    }

    /**
     * Validate input and fill $errors array with proper email error text to be displayed if it fails.
     * 
     * @param string $email User email.
     * @param Array[string] &$errors Error array passed by reference to be modified in-function.
     * @return bool True if properly filled-in.
     */
    private function validate_email_input($email, &$errors): bool
    {
        if ($email  === '') {
            $errors[Login::EMAIL_KEY] = Login::EMAIL_EMPTY;
            return false;
        }
        $email = filter_var($email, FILTER_VALIDATE_EMAIL);
        if (!$email) {
            $errors[Login::EMAIL_KEY] = Login::EMAIL_INVALID;
            return false;
        }
        return true;
    }

    /**
     * Validate input and fill $errors array with proper password error text to be displayed if it fails.
     * 
     * @param string $password User password.
     * @param Array[string] &$errors Error array passed by reference to be modified in-function.
     * @return bool True if properly filled;
     */
    private function validate_password_input($password, &$errors)
    {
        if ($password === '') {
            $errors[Login::PASSWORD_KEY] = Login::PASSWORD_EMPTY;
            return false;
        }
        return true;
    }

    /**
     * Send a new password to user and register new password to database
     * 
     * @param string $email User email.
     */
    private function handleNewPasswordRequest($email)
    {
        echo 'handleNewPasswordRequest not implemented';
    }
}
