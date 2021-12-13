<?php

################################
## Joël Piguet - 2021.12.13 ###
##############################

namespace app\routes;

use app\constants\Alert;
use app\constants\AlertType;
use app\constants\LogInfo;
use app\constants\Route;
use app\constants\Warning;
use app\helpers\Authenticate;
use app\helpers\Database;
use app\helpers\Logging;
use app\helpers\Util;
use app\helpers\Validation;

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

        $user  = Authenticate::getUser();
        $user_id = Authenticate::getUserId();

        if (!$user) {
            $this->setAlert(AlertType::FAILURE, Alert::USER_NOT_FOUND);
            return "";
        }

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
            $contact_email = $user->getContactEmail();
            if ($contact_email === '') {
                $contact_email = $user->getEmail();
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

            if (Validation::validateAlias($this, $alias)) {
                $display = 0;

                if ($alias === $user->getAlias()) {
                    // changed nothing
                    goto end;
                }

                $alias_arg = $alias ? $alias : $user->getEmail();

                $existing = Database::users()->queryByAlias($alias_arg);
                if ($existing) {
                    if ($existing->getId() !== $user_id) {
                        // alias already exists and assigned to another user.
                        $this->setAlert(AlertType::FAILURE, Alert::ALIAS_EXISTS_FAILURE);
                        goto end;
                    }
                }

                if (Database::users()->updateAlias($user_id, $alias_arg)) {

                    Logging::info(LogInfo::USER_UPDATED, [
                        'user-id' => $user_id,
                        'new-alias' => $alias_arg
                    ]);

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

            if (Validation::validatePassword($this, $password)) {
                if (Validation::validatePasswordRepeat($this, $password)) {

                    $display = 0;
                    $encrypted = util::encryptPassword($password);

                    if (Database::users()->updatePassword($user_id, $encrypted)) {

                        Logging::info(LogInfo::USER_UPDATED, [
                            'user-id' => $user_id,
                            'new-password' => '*********'
                        ]);

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

            if (Validation::validateContactEmail($this, $contact_email)) {

                $display = 0;

                if ($contact_email === $user->getEmail()) {
                    $contact_email  = '';
                }

                if (Database::users()->updateContactEmail($user_id, $contact_email)) {

                    Logging::info(LogInfo::USER_UPDATED, [
                        'user-id' => $user_id,
                        'new-contact-email' => $contact_email
                    ]);

                    // if contact is null or empty, then contact is the login email.
                    if (strlen($contact_email) > 0) {
                        $this->setAlert(AlertType::SUCCESS, sprintf(Alert::CONTACT_SET_SUCCESS, $contact_email));
                    } else {
                        $this->setAlert(AlertType::SUCCESS, sprintf(Alert::CONTACT_RESET_SUCCESS, $user->getEmail()));
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
                $this->setError('delays',  Warning::DELAYS_NONE);
            } else {

                $display = 0;
                $str = implode('-', $delays);

                if (Database::users()->updateContactDelay($user_id, $str)) {

                    Logging::info(LogInfo::USER_UPDATED, [
                        'user-id' => $user_id,
                        'new-contact-delays' => $str
                    ]);

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
            'login_email' => $user->getEmail(),
            'contact_email' => $contact_email ?? '',
            'delays' => $delays ?? [],
        ]);
    }
}
