<?php

################################
## Joël Piguet - 2022.01.11 ###
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
        parent::__construct(Route::PROFILE, 'profile_template', 'profile_script');
    }

    public function getBodyContent(): string
    {
        if (!Authenticate::isLoggedIn()) {
            $this->requestRedirect(Route::LOGIN);
            return '';
        }

        $user  = Authenticate::getUser();
        if (!$user) {
            return $this->requestRedirect(Route::HOME);
        }

        logging::debug('getBodyContent_alias: ' . $user->getAlias());
        $alias = $user->getAlias();
        $contact_email = $user->getContactEmail();
        if ($contact_email === '') {
            $contact_email = $user->getLoginEmail();
        }
        $delays = $user->getContactDelays();
        $user_id = $user->getId();



        // if (isset($_POST['set-email'])) {


        //     if (Validation::validateContactEmail($this, $contact_email)) {


        //         if ($contact_email === $user->getLoginEmail()) {
        //             $contact_email  = '';
        //         }

        //         if (Database::users()->updateContactEmail($user_id, $contact_email)) {

        //             Logging::info(LogInfo::USER_UPDATED, [
        //                 'user-id' => $user_id,
        //                 'new-contact-email' => $contact_email
        //             ]);

        //             // if contact is null or empty, then contact is the login email.
        //             if (strlen($contact_email) > 0) {
        //                 $this->showAlert(AlertType::SUCCESS, sprintf(Alert::CONTACT_SET_SUCCESS, $contact_email));
        //             } else {
        //                 $this->showAlert(AlertType::SUCCESS, sprintf(Alert::CONTACT_RESET_SUCCESS, $user->getLoginEmail()));
        //             }
        //         } else {
        //             $this->showAlert(AlertType::FAILURE,  Alert::CONTACT_SET_FAILURE);
        //         }
        //     }
        //     goto end;
        // }

        // if (isset($_POST['set-delay'])) {

        //     $delays = [];
        //     if (isset($_POST['delay-3'])) {
        //         array_push($delays, 3);
        //     }
        //     if (isset($_POST['delay-7'])) {
        //         array_push($delays, 7);
        //     }
        //     if (isset($_POST['delay-14'])) {
        //         array_push($delays, 14);
        //     }
        //     if (isset($_POST['delay-30'])) {
        //         array_push($delays, 30);
        //     }

        //     if (count($delays) == 0) {
        //         $this->showWarning('delays',  Warning::DELAYS_NONE);
        //     } else {

        //         $str = implode('-', $delays);

        //         if (Database::users()->updateContactDelay($user_id, $str)) {

        //             Logging::info(LogInfo::USER_UPDATED, [
        //                 'user-id' => $user_id,
        //                 'new-contact-delays' => $str
        //             ]);

        //             $this->showAlert(AlertType::SUCCESS, Alert::DELAY_SET_SUCCESS);
        //         } else {
        //             $this->showAlert(AlertType::FAILURE, Alert::DELAY_SET_FAILURE);
        //         }
        //     }
        // }

        // end:

        return $this->renderTemplate([
            'alias' => $alias ?? '',
            'password' => $password_plain ?? '',
            'password_repeat' => $password_repeat ?? '',
            'login_email' => $user->getLoginEmail(),
            'contact_email' => $contact_email ?? '',
            'delays' => $delays ?? [],
        ]);
    }
}
