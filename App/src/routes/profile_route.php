<?php

################################
## Joël Piguet - 2021.11.29 ###
##############################

namespace routes;

use helpers\Authenticate;
use helpers\Database;
use helpers\Util;


/**
 * Route class containing behavior linked to profile_template. This route displays user info.
 */
class ProfileRoute extends BaseRoute
{
    const REPEAT_EMPTY = "Il vous faut répéter votre mot de passe";
    const DIFFERENT_PASSWORD = "Ce mot de passe n'est pas identique au précédent.";

    const PASSWORD_UPDATE_FAILURE = "Le changement de mot de passe a échoué.";
    const PASSWORD_UPDATE_SUCCESS = "Le mot de passe a été modifié avec succès.";

    const CONTACT_SET_FAILURE = "Le changement d'adresse de contact a échoué.";
    const CONTACT_SET_SUCCESS = "Votre nouvelle adresse de contact [%s] a été définie avec succès.";
    const CONTACT_RESET_SUCCESS = "Vos e-mail de rappels sont désormais envoyé à [%s].";

    function __construct()
    {
        parent::__construct('profile_template', PROFILE);
    }

    public function getBodyContent(): string
    {
        if (!Authenticate::isLoggedIn()) {
            $this->requestRedirect(LOGIN);
            return '';
        }


        $display = 0;
        $errors = [];

        if (isset($_GET['change_password'])) {
            $display = 1;
        } else if (isset($_GET['add_email'])) {
            $display = 2;
            $login_email = Authenticate::getUser()->getEmail();
            $contact_email = Authenticate::getUser()->getContactEmail();
            if ($contact_email === '') {
                $contact_email = $login_email;
            }
        } else if (isset($_GET['modify_delay'])) {
            $display = 3;
            $delays = Authenticate::getUser()->getContactDelays();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_POST['new-password'])) {
                $display = 1;

                if (Util::validate_password($password, $errors)) {
                    if ($this->validateRepeat($password, $errors)) {

                        $user_id = Authenticate::getUserId();
                        $encrypted = util::encryptPassword($password);

                        if (Database::getInstance()->updateUserPassword($user_id, $encrypted)) {
                            $alert = [
                                'type' => 'success',
                                'msg' => ProfileRoute::PASSWORD_UPDATE_SUCCESS,
                            ];
                        } else {
                            $alert = [
                                'type' => 'warning',
                                'msg' => ProfileRoute::PASSWORD_UPDATE_FAILURE,
                            ];
                        }
                        $display = 0;
                    }
                }
            } else if (isset($_POST['set-email'])) {
                $display = 2;
                $login_email = Authenticate::getUser()->getEmail();
                error_log('c: ' .  $login_email);

                if ($this->validateContactEmail($contact_email, $errors)) {

                    $display = 0;
                    $user_id = Authenticate::getUserId();

                    if ($contact_email === $login_email) {
                        $contact_email  = '';
                    }

                    if (Database::getInstance()->updateUserContactEmail($user_id, $contact_email)) {

                        // if contact is null or empty, then contact is the login email.
                        if (strlen($contact_email) > 0) {
                            $alert = [
                                'type' => 'success',
                                'msg' => sprintf(ProfileRoute::CONTACT_SET_SUCCESS, $contact_email),
                            ];
                        } else {
                            $alert = [
                                'type' => 'success',
                                'msg' => sprintf(ProfileRoute::CONTACT_RESET_SUCCESS, $login_email),
                            ];
                        }
                    } else {
                        $alert = [
                            'type' => 'warning',
                            'msg' => ProfileRoute::CONTACT_SET_FAILURE,
                        ];
                    }
                }
            } else if (isset($_POST['set-delay'])) {
                $display = 3;
                var_dump($_POST);
                return 'not implemented';
            }
        }

        return $this->renderTemplate([
            'alert' => $alert ?? '',
            'display' => $display,
            'errors' => $errors,
            'values' => [
                'password' => $password ?? '',
                'password-repeat' => $password_repeat ?? '',
                'login-email' => $login_email ?? '',
                'contact-email' => $contact_email ?? '',
                'delays' => $delays ?? [],
            ],
        ]);
    }

    /**
     * Validate contact email. Email must a valid email format or set to null.
     * 
     * @param string|null $contact_email Contact e-mail by reference.
     * @param Array[string] &$errors Error array passed by reference to be modified in-function.
     * @return bool True if e-mail is set to empty string or is a valid email format.
     */
    private function validateContactEmail(&$email, array &$errors): bool
    {
        $email = trim($_POST['contact-email']) ?? '';

        if ($email  === '') {
            return true;
        }
        $email = filter_var($email, FILTER_VALIDATE_EMAIL);
        if (!$email) {
            $errors['contact-email'] = Login::EMAIL_INVALID;
            return false;
        }
        return true;
    }

    /**
     * Validate the repeated password.
     * 
     * @param string $password_first Proposed user password entered in first field.
     * @param Array[string] &$errors Error array passed by reference to be modified in-function.
     * @return bool True if repeat-password corresponds to first entry.
     */
    private function validateRepeat(string $password_first, array &$errors): bool
    {
        $password_repeat = trim($_POST['password-repeat']) ?? '';
        if (!$password_repeat) {
            $errors['password-repeat'] = ProfileRoute::REPEAT_EMPTY;
            return false;
        }

        if ($password_first !== $password_repeat) {
            $errors['password-repeat'] = ProfileRoute::DIFFERENT_PASSWORD;
            return false;
        }
        return true;
    }
}
