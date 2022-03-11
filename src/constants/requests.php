<?php

declare(strict_types=1);

################################
## Joël Piguet - 2022.01.17 ###
##############################

namespace app\constants;

class Requests
{
    const DELETE_ARTICLE = '/request?deletearticle=';
    const DELETE_LOC_PRESET = 'request?deletelocpreset=';
    const DELETE_USER = '/request?deleteuser=';
    const LOGOUT = '/request?logout=true';
    const FORGOTTEN_PASSWORD = '/request?forgottenpassword=';
    const RENEW_USER_PASSWORD = '/request?renewuserpassword=';
}
