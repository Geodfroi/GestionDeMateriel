<?php

################################
## Joël Piguet - 2021.11.29 ###
##############################

namespace routes;

use helpers\Database;
use helpers\Authenticate;

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
        parent::__construct('login_template', LOGIN);
    }

    public function getBodyContent(): string
    {
        $errors = [];

        if (Authenticate::isLoggedIn()) {

            if (isset($_GET['logout'])) {
                Authenticate::logout();
                $alert = [
                    'type' => 'info',
                    'msg' => "L'usager précédent s'est déconnecté.",
                ];
            } else {
                $this->requestRedirect(HOME);
                return '';
            }
        }

        if (isset($_GET['old-email'])) {

            // handle demand for new password.
            $email  = $_GET['old-email'];
            $user = Database::getInstance()->getUserByEmail($email);

            if (isset($user)) {
                // Un nouveau mot de passe a été envoyé à '<?php echo $values['email'] 
                // $this->handleNewPasswordRequest($user->getEmail());
                $alert = [
                    'type' => 'warning',
                    'msg' => "renew-password not implemented",
                ];
            }
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            if ($this->validateEmailInput($email, $errors)) {
                $user = Database::getInstance()->getUserByEmail($email);
            }

            if (!isset($user)) {
                $errors['email'] = Login::USER_NOT_FOUND;
            } else {
                if ($this->validatePasswordInput($password, $errors)) {
                    if ($user->verifyPassword($password)) {
                        Authenticate::login($user);
                        $this->requestRedirect(HOME);
                        return "";
                    } else {
                        $errors['password'] = Login::PASSWORD_INVALID;
                    }
                }
            }
        }

        return $this->renderTemplate([
            'errors' => $errors,
            'values' => [
                'email' => $email ?? '',
                'password' => $password ?? '',
            ],
            'alert' => $alert ?? '',
        ]);
    }

    /**
     * Validate input and fill $errors array with proper email error text to be displayed if it fails.
     * 
     * @param string $email User email by reference.
     * @param Array[string] &$errors Error array passed by reference to be modified in-function.
     * @return bool True if properly filled-in.
     */
    private function validateEmailInput(&$email, &$errors): bool
    {
        $email = trim($_POST['email']) ?? '';

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
     * @param string $password User password by reference
     * @param Array[string] &$errors Error array passed by reference to be modified in-function.
     * @return bool True if properly filled;
     */
    private function validatePasswordInput(&$password, &$errors)
    {
        $password = trim($_POST['password']) ?? '';
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
        echo 'login_route:: handleNewPasswordRequest not implemented';
    }
}
