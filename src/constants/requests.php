<?php

declare(strict_types=1);

################################
## Joël Piguet - 2022.03.14 ###
##############################

namespace app\constants;


class Requests
{
    const DELETE_ARTICLE = APP_URL . '/requests?deletearticle=';
    const DELETE_LOC_PRESET =  APP_URL . 'requests?deletelocpreset=';
    const DELETE_USER =  APP_URL . '/requests?deleteuser=';
    const LOGOUT =  APP_URL . '/requests?logout=true';
    const FORGOTTEN_PASSWORD =  APP_URL . '/requests?forgottenpassword=';
    const RENEW_USER_PASSWORD =  APP_URL . '/requests?renewuserpassword=';
}
