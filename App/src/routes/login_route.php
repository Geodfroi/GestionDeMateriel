<?php

################################
## Joël Piguet - 2021.11.15 ###
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

    /**
     * Fill $form_errors array with proper email error text to be displayed.
     * 
     * @param string $email User email.
     * @param Array[string] &$form_errors Error array passed by reference to be modified in-function.
     */
    private function handleEmailError($email, &$form_errors)
    {
        if ($email  === '') {
            $form_errors['email'] = EMAIL_EMPTY;
            return;
        }
        $email = filter_var($email, FILTER_VALIDATE_EMAIL);
        if (!$email) {
            $form_errors['email'] = EMAIL_INVALID;
            return;
        }
        $form_errors['email'] = USER_NOT_FOUND;
    }

    /**
     * Fill $form_errors array with proper password error text to be displayed.
     * 
     * @param string $password User password.
     * @param Array[string] &$form_errors Error array passed by reference to be modified in-function.
     */
    private function handlePasswordError($password, &$form_errors)
    {
        if ($password === '') {
            $form_errors['password'] = PASSWORD_EMPTY;
            return;
        }
        $form_errors['password'] = PASSWORD_INVALID;
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
            $email  = $_GET['old-email'];
            $user = Database::getInstance()->getUserByEmail($email);
            if (isset($user)) {
                $this->handleNewPasswordRequest($user->getEmail());
                $password_changed = true;
            }
        } else if (count($_POST)) {
            $email  = trim($_POST['email']) ?? '';
            $password = trim($_POST['password']) ?? '';

            $user = Database::getInstance()->getUserByEmail($email);

            if (!isset($user))
                $this->handleEmailError($email, $form_errors);
            else {
                if (!$user->verifyPassword($password)) {
                    $this->handlePasswordError($password, $form_errors);
                } else {
                    Authenticate::login($user);
                    $this->requestRedirect(Routes::ARTICLES);
                    return "";
                }
            }
        }
        return $this->renderTemplate([
            'form_errors' => $form_errors,
            'email' => $email,
            'password_changed' => $password_changed,
        ]);
    }
}
