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
