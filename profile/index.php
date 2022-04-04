<?php

declare(strict_types=1);

################################
## Joël Piguet - 2022.04.04 ###
##############################

use app\constants\Route;
use app\helpers\Authenticate;
use app\helpers\BaseRoute;
use app\helpers\Logging;
use app\helpers\Util;


require_once __DIR__ . '/../loader.php';
require_once __DIR__ . '/../vendor/autoload.php'; // use composer to load autofile.
session_start();

/**
 * Route class containing behavior linked to profile_template. This route displays user info.
 */
class Profile extends BaseRoute
{
    function __construct()
    {
        parent::__construct('profile', 'profile_template', 'profile_script');
    }

    public function getBodyContent(): string
    {
        $user  = Authenticate::getUser();
        if (!$user) {
            return $this->requestRedirect(Route::HOME);
        }
        $contact_email = $user->getContactEmail();
        if ($contact_email === '') {
            $contact_email = $user->getLoginEmail();
        }

        return $this->renderTemplate(['login_email' => $user->getLoginEmail()]);
    }
}

Logging::debug("profile route");
if (!Authenticate::isLoggedIn()) {
    Util::requestRedirect(Route::LOGIN);
} else {
    echo (new Profile())->renderRoute();
}
