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
        } else if (isset($_GET['modify_delay'])) {
            $display = 3;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            if (isset($_POST['new-password'])) {
                $display = 1;

                if (Util::validate_password($password, $errors)) {
                    if ($this->validate_repeat($password, $errors)) {

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
            }
        }

        return $this->renderTemplate([
            'alert' => $alert ?? '',
            'display' => $display,
            'errors' => $errors,
            'values' => [
                'password' => $password ?? '',
                'password-repeat' => $password_repeat ?? '',
            ],

        ]);
    }

    /**
     * Validate the repeated password.
     * 
     * @param string $password_first Proposed user password entered in first field.
     * @param Array[string] &$errors Error array passed by reference to be modified in-function.
     * @return bool True if repeat-password corresponds to first entry.
     */
    private function validate_repeat(string $password_first, array &$errors): bool
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
