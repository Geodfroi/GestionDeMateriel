<?php

################################
## Joël Piguet - 2021.12.02 ###
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
    function __construct()
    {
        parent::__construct(PROFILE_TEMPLATE, PROFILE);
    }

    public function getBodyContent(): string
    {
        if (!Authenticate::isLoggedIn()) {
            $this->requestRedirect(LOGIN);
            return '';
        }

        $user = Authenticate::getUser();
        $user_id = $user->getId();

        // display variable identify which sub-section of the templates is displayed.
        $display = 0;

        if (isset($_GET['set_alias'])) {
            $display = 1;
            $alias = $user->getAlias();
            goto end;
        }

        if (isset($_GET['change_password'])) {
            $display = 2;
            goto end;
        }

        if (isset($_GET['add_email'])) {
            $display = 3;
            $login_email = $user->getEmail();
            $contact_email = $user->getContactEmail();
            if ($contact_email === '') {
                $contact_email = $login_email;
            }
            goto end;
        }

        if (isset($_GET['modify_delay'])) {
            $display = 4;
            $delays = $user->getContactDelays();
            goto end;
        }

        if (isset($_POST['set-alias'])) {
            $display = 1;
            if ($this->validateAlias($alias)) {
                $display = 0;
                if ($alias !== $user->getAlias()) {
                    if (Database::users()->updateAlias($user_id, $alias)) {

                        if (strlen($alias) > 0) {
                            $this->setAlert(AlertType::SUCCESS, ALIAS_UPDATE_SUCCESS);
                        } else {
                            $this->setAlert(AlertType::SUCCESS, ALIAS_DELETE_SUCCESS);
                        }
                    } else {
                        $this->setAlert(AlertType::FAILURE, ALIAS_UPDATE_FAILURE);
                    }
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

                    if (Database::users()->updatePassword($user_id, $encrypted)) {
                        $this->setAlert(AlertType::SUCCESS, PASSWORD_UPDATE_SUCCESS);
                    } else {
                        $this->setAlert(AlertType::FAILURE, PASSWORD_UPDATE_FAILURE);
                    }
                }
            }
            goto end;
        }

        if (isset($_POST['set-email'])) {
            $display = 3;
            $login_email = Authenticate::getUser()->getEmail();

            if ($this->validateContactEmail($contact_email)) {

                $display = 0;
                $user_id = Authenticate::getUserId();

                if ($contact_email === $login_email) {
                    $contact_email  = '';
                }

                if (Database::users()->updateContactEmail($user_id, $contact_email)) {

                    // if contact is null or empty, then contact is the login email.
                    if (strlen($contact_email) > 0) {
                        $this->setAlert(AlertType::SUCCESS, sprintf(CONTACT_SET_SUCCESS, $contact_email));
                    } else {
                        $this->setAlert(AlertType::SUCCESS, sprintf(CONTACT_RESET_SUCCESS, $login_email));
                    }
                } else {
                    $this->setAlert(AlertType::FAILURE,  CONTACT_SET_FAILURE);
                }
            }
            goto end;
        }

        if (isset($_POST['set-delay'])) {
            $display = 4;

            $delays = [];
            if (isset($_POST['delay-3'])) {
                array_push($delays, '3');
            }
            if (isset($_POST['delay-7'])) {
                array_push($delays, '7');
            }
            if (isset($_POST['delay-14'])) {
                array_push($delays, '14');
            }
            if (isset($_POST['delay-30'])) {
                array_push($delays, '30');
            }

            if (count($delays) == 0) {
                $this->setError('delays',  DELAYS_NONE);
            } else {

                $display = 0;
                $str = implode('-', $delays);

                if (Database::users()->updateContactDelay($user_id, $str)) {
                    $this->setAlert(AlertType::SUCCESS,  DELAY_SET_SUCCESS);
                } else {
                    $this->setAlert(AlertType::FAILURE,  DELAY_SET_FAILURE);
                }
            }
        }

        end:

        return $this->renderTemplate([
            'display' => $display,
            'values' => [
                'alias' => $alias ?? '',
                'password' => $password ?? '',
                'password-repeat' => $password_repeat ?? '',
                'login-email' => $login_email ?? '',
                'contact-email' => $contact_email ?? '',
                'delays' => $delays ?? [],
            ],
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
        if (strlen($alias) < ALIAS_MIN_LENGHT) {
            $this->setError('alias', sprintf(ALIAS_TOO_SHORT, ALIAS_MIN_LENGHT));
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
            $this->setError('contact-email', LOGIN_EMAIL_INVALID);
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
            $this->setError('password-repeat', PASSWORD_REPEAT_NULL);
            return false;
        }

        if ($password_first !== $password_repeat) {
            $this->setError('password-repeat', PASSWORD_DIFFERENT);
            return false;
        }
        return true;
    }
}
