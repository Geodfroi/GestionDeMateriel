<?php

################################
## Joël Piguet - 2021.12.09 ###
##############################

namespace app\routes;

use app\constants\Alert;
use app\constants\Error;
use app\constants\Route;
use app\constants\Session;
use app\constants\Settings;
use app\helpers\Authenticate;
use app\helpers\Database;
use app\helpers\Util;

use Exception;

/**
 * Route class containing behavior linked to profile_template. This route displays user info.
 */
class Profile extends BaseRoute
{
    function __construct()
    {
        parent::__construct('profile_template', Route::PROFILE);
    }

    public function getBodyContent(): string
    {
        if (!Authenticate::isLoggedIn()) {
            $this->requestRedirect(Route::LOGIN);
            return '';
        }

        // display variable identify which sub-section of the templates is displayed.
        $display = 0;

        $profile_user  = Authenticate::getUser();
        $profile_user_id = Authenticate::getUserId();

        if (!$profile_user) {
            $this->setAlert(AlertType::FAILURE, Alert::USER_NOT_FOUND);
            return "";
        }

        if (isset($_GET['set_alias'])) {
            $display = 1;
            $alias = $profile_user->getAlias();
            goto end;
        }

        if (isset($_GET['change_password'])) {
            $display = 2;
            goto end;
        }

        if (isset($_GET['add_email'])) {
            $display = 3;
            $contact_email = $profile_user->getContactEmail();
            if ($contact_email === '') {
                $contact_email = $profile_user->getEmail();
            }
            goto end;
        }

        if (isset($_GET['modify_delay'])) {
            $display = 4;
            $delays = $profile_user->getContactDelays();
            goto end;
        }

        if (isset($_POST['set-alias'])) {
            $display = 1;

            if ($this->validateAlias($alias)) {
                $display = 0;

                if ($alias === $profile_user->getAlias()) {
                    // changed nothing
                    goto end;
                }

                $alias_arg = $alias ? $alias : $profile_user->getEmail();

                $existing = Database::users()->queryByAlias($alias_arg);
                if ($existing) {
                    if ($existing->getId() !== $profile_user_id) {
                        // alias already exists and assigned to another user.
                        $this->setAlert(AlertType::FAILURE, Alert::ALIAS_EXISTS_FAILURE);
                        goto end;
                    }
                }

                if (Database::users()->updateAlias($profile_user_id, $alias_arg)) {
                    if ($alias) {
                        $this->setAlert(AlertType::SUCCESS, Alert::ALIAS_UPDATE_SUCCESS);
                    } else {
                        $this->setAlert(AlertType::SUCCESS, Alert::ALIAS_DELETE_SUCCESS);
                    }
                } else {
                    $this->setAlert(AlertType::FAILURE, Alert::ALIAS_UPDATE_FAILURE);
                }
            }
            goto end;
        }

        if (isset($_POST['new-password'])) {
            $display = 2;

            if (Util::validatePassword($this, $password)) {
                if ($this->validateRepeat($password)) {

                    $display = 0;
                    $encrypted = util::encryptPassword($password);

                    if (Database::users()->updatePassword($profile_user_id, $encrypted)) {
                        $this->setAlert(AlertType::SUCCESS, Alert::PASSWORD_UPDATE_SUCCESS);
                    } else {
                        $this->setAlert(AlertType::FAILURE, Alert::PASSWORD_UPDATE_FAILURE);
                    }
                }
            }
            goto end;
        }

        if (isset($_POST['set-email'])) {
            $display = 3;

            if ($this->validateContactEmail($contact_email)) {

                $display = 0;

                if ($contact_email === $profile_user->getEmail()) {
                    $contact_email  = '';
                }

                if (Database::users()->updateContactEmail($profile_user_id, $contact_email)) {

                    // if contact is null or empty, then contact is the login email.
                    if (strlen($contact_email) > 0) {
                        $this->setAlert(AlertType::SUCCESS, sprintf(Alert::CONTACT_SET_SUCCESS, $contact_email));
                    } else {
                        $this->setAlert(AlertType::SUCCESS, sprintf(Alert::CONTACT_RESET_SUCCESS, $profile_user->getEmail()));
                    }
                } else {
                    $this->setAlert(AlertType::FAILURE,  Alert::CONTACT_SET_FAILURE);
                }
            }
            goto end;
        }

        if (isset($_POST['set-delay'])) {
            $display = 4;

            $delays = [];
            if (isset($_POST['delay-3'])) {
                array_push($delays, 3);
            }
            if (isset($_POST['delay-7'])) {
                array_push($delays, 7);
            }
            if (isset($_POST['delay-14'])) {
                array_push($delays, 14);
            }
            if (isset($_POST['delay-30'])) {
                array_push($delays, 30);
            }

            if (count($delays) == 0) {
                $this->setError('delays',  Error::DELAYS_NONE);
            } else {

                $display = 0;
                $str = implode('-', $delays);

                if (Database::users()->updateContactDelay($profile_user_id, $str)) {
                    $this->setAlert(AlertType::SUCCESS, Alert::DELAY_SET_SUCCESS);
                } else {
                    $this->setAlert(AlertType::FAILURE, Alert::DELAY_SET_FAILURE);
                }
            }
        }

        end:

        return $this->renderTemplate([
            'display' => $display,
            'alias' => $alias ?? '',
            'password' => $password ?? '',
            'password_repeat' => $password_repeat ?? '',
            'login_email' => $profile_user->getEmail(),
            'contact_email' => $contact_email ?? '',
            'delays' => $delays ?? [],
        ]);
    }

    /**
     * Validate user alias. Alias can be set to empty string in which cas e-mail root is used in the app.
     * 
     * @param string|null $alias Optional alias by reference.
     * @return bool True if Alias is conform or empty.
     */
    private function validateAlias(&$alias): bool
    {
        $alias = trim($_POST['alias']) ?? '';
        if ($alias === '') {
            return true;
        }
        if (strlen($alias) < Settings::ALIAS_MIN_LENGHT) {
            $this->setError('alias', sprintf(Error::ALIAS_TOO_SHORT, Settings::ALIAS_MIN_LENGHT));
            return false;
        }
        return true;
    }

    /**
     * Validate contact email. Email must a valid email format or set to null.
     * 
     * @param string|null $contact_email Contact e-mail by reference.
     * @return bool True if e-mail is set to empty string or is a valid email format.
     */
    private function validateContactEmail(&$email): bool
    {
        $email = trim($_POST['contact-email']) ?? '';

        if ($email  === '') {
            return true;
        }
        $email = filter_var($email, FILTER_VALIDATE_EMAIL);
        if (!$email) {
            $this->setError('contact-email', Error::LOGIN_EMAIL_INVALID);
            return false;
        }
        return true;
    }

    /**
     * Validate the repeated password.
     * 
     * @param string $password_first Proposed user password entered in first field.
     * @return bool True if repeat-password corresponds to first entry.
     */
    private function validateRepeat(string $password_first): bool
    {
        $password_repeat = trim($_POST['password-repeat']) ?? '';
        if (!$password_repeat) {
            $this->setError('password-repeat', Error::PASSWORD_REPEAT_NULL);
            return false;
        }

        if ($password_first !== $password_repeat) {
            $this->setError('password-repeat', Error::PASSWORD_DIFFERENT);
            return false;
        }
        return true;
    }
}

        // if (!isset($_SESSION[SESSION::PROFILE_ID])) {
        //     $_SESSION[SESSION::PROFILE_ID] = Authenticate::getUserId();
        // }

        // $profile_user_id  =  $_SESSION[SESSION::PROFILE_ID];
        // $profile_user  = Database::users()->queryById($profile_user_id);
        // $profile_user_id  =  $_SESSION[SESSION::PROFILE_ID];
