<?php

declare(strict_types=1);

################################
## Joël Piguet - 2022.01.16 ###
##############################

namespace app\constants;

class Requests
{
    const DELETE_USER = '/request?deleteuser=';
    const LOGOUT = '/request?logout=true';
    const FORGOTTEN_PASSWORD = '/request?forgottenpassword=';
    const RENEW_USER_PASSWORD = '/request?renewuserpassword=';
}
