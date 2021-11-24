<?php

################################
## Joël Piguet - 2021.11.24 ###
##############################

namespace routes;

use helpers\Database;
use helpers\Authenticate;
use routes\Routes;

/**
 * Route class containing behavior linked to login_template
 */
class Login extends BaseRoute
{
    const EMAIL_EMPTY = 'Un e-mail est nécessaire pour vous connecter.';
    const PASSWORD_EMPTY = 'Il vous faut fournir un mot de passe.';
    const EMAIL_INVALID = "Il ne s'agit pas d'une adresse e-mail valide.";
    const USER_NOT_FOUND = "Il n'existe pas d'usager employant cette adresse e-mail.";
    const PASSWORD_INVALID = "Le mot de passe n'est pas correct.";

    public function __construct()
    {
        parent::__construct('login_template', Routes::LOGIN);
    }


    public function getBodyContent(): string
    {
        $values = [
            'email' => '',
            'password' => '',
        ];
        $errors = [];
        $alerts = [];
        $user = '';

        if (Authenticate::isLoggedIn()) {

            if (isset($_GET['logout'])) {
                Authenticate::logout();
                $alerts['logout'] = '';
            } else {

                $user = Authenticate::getUser();
                if ($user->isAdmin()) {
                    $this->requestRedirect(Routes::ADMIN);
                } else {
                    $this->requestRedirect(Routes::ART_TABLE);
                }

                return '';
            }
        }

        if (isset($_GET['old-email'])) {
            // handle demand for new password.
            $values['email']  = $_GET['old-email'];
            $user = Database::getInstance()->getUserByEmail($values['email']);

            if (isset($user)) {
                $this->handleNewPasswordRequest($user->getEmail());
                $alerts['new-password'] = '';
            }
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            // handle login post request.
            $values['email'] = trim($_POST['email']) ?? '';
            $values['password'] = trim($_POST['password']) ?? '';

            if ($this->validate_email_input($values['email'], $errors)) {
                $user = Database::getInstance()->getUserByEmail($values['email']);
            }

            if (!isset($user)) {
                $errors['email'] = Login::USER_NOT_FOUND;
            } else {
                if ($this->validate_password_input($values['password'], $errors)) {
                    if ($user->verifyPassword($values['password'])) {
                        Authenticate::login($user);
                        if ($user->isAdmin()) {
                            $this->requestRedirect(Routes::ADMIN);
                        } else {
                            $this->requestRedirect(Routes::ART_TABLE);
                        }
                        return "";
                    } else {
                        $errors['password'] = Login::PASSWORD_INVALID;
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
            $errors['email'] = Login::EMAIL_EMPTY;
            return false;
        }
        $email = filter_var($email, FILTER_VALIDATE_EMAIL);
        if (!$email) {
            $errors['email'] = Login::EMAIL_INVALID;
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
            $errors['password'] = Login::PASSWORD_EMPTY;
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
