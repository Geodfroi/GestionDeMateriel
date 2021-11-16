<?php

################################
## Joël Piguet - 2021.11.16 ###
##############################

namespace routes;

use Exception;
use helpers\Database;
use helpers\Authenticate;
use routes\Routes;

const EMAIL_EMPTY = 'Un e-mail est nécessaire pour vous connecter.';
const PASSWORD_EMPTY = 'Il vous faut fournir un mot de passe.';
const EMAIL_INVALID = "Il ne s'agit pas d'une adresse e-mail valide.";
const USER_NOT_FOUND = "Il n'existe pas d'usager employant cette adresse e-mail.";
const PASSWORD_INVALID = "Le mot de passe n'est pas correct.";


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
        if (Authenticate::isLoggedIn()) {
            $this->requestRedirect(Routes::ARTICLES);
            return '';
        }

        $form_errors = [];
        $email  = '';
        $user = '';
        $password_changed = false;

        if (isset($_GET['old-email'])) {
            // handle demand for new password.
            $email  = $_GET['old-email'];
            $user = Database::getInstance()->getUserByEmail($email);

            if (isset($user)) {
                $this->handleNewPasswordRequest($user->getEmail());
                $password_changed = true;
            }
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // handle login post request.
            $email  = trim($_POST['email']) ?? '';
            $password = trim($_POST['password']) ?? '';

            if ($this->validate_email_input($email, $form_errors)) {
                $user = Database::getInstance()->getUserByEmail($email);
            }

            if (!isset($user)) {
                $form_errors['email'] = USER_NOT_FOUND;
            } else {
                if ($this->validate_password_input($password, $form_errors)) {
                    if ($user->verifyPassword($password)) {
                        Authenticate::login($user);
                        $this->requestRedirect(Routes::ARTICLES);
                        return "";
                    } else {
                        $form_errors['password'] = PASSWORD_INVALID;
                    }
                }
            }
        }
        return $this->renderTemplate([
            'email' => $email,
            'form_errors' => $form_errors,
            'password_changed' => $password_changed,
        ]);
    }

    /**
     * Validate input and fill $form_errors array with proper email error text to be displayed if it fails.
     * 
     * @param string $email User email.
     * @param Array[string] &$form_errors Error array passed by reference to be modified in-function.
     * @return bool True if properly filled-in.
     */
    private function validate_email_input($email, &$form_errors): bool
    {
        if ($email  === '') {
            $form_errors['email'] = EMAIL_EMPTY;
            return false;
        }
        $email = filter_var($email, FILTER_VALIDATE_EMAIL);
        if (!$email) {
            $form_errors['email'] = EMAIL_INVALID;
            return false;
        }
        return true;
    }

    /**
     * Validate input and fill $form_errors array with proper password error text to be displayed if it fails.
     * 
     * @param string $password User password.
     * @param Array[string] &$form_errors Error array passed by reference to be modified in-function.
     * @return bool True if properly filled;
     */
    private function validate_password_input($password, &$form_errors)
    {
        if ($password === '') {
            $form_errors['password'] = PASSWORD_EMPTY;
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
        throw new Exception('not implemented');
    }
}
