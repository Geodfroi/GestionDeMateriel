<?php

declare(strict_types=1);

################################
## Joël Piguet - 2022.04.04 ###
##############################

namespace app\constants;

class Route
{
    // routes
    const ADMIN = APP_URL . '/admin';
    const ART_TABLE = APP_URL  . '/articletable';
    const ART_EDIT = APP_URL . '/articleedit';
    const DEBUG_EMAILS = APP_URL . '/debugmails';
    const DEBUG_PAGE = APP_URL . '/debugpage';
    const CONTACT = APP_URL . '/contact';
    const LOCAL_PRESETS =  APP_URL . '/locationpresets';
    const LOGIN = APP_URL . '/login';
    const HOME = APP_URL;
    const PROFILE = APP_URL . '/profile';
    const USER_EDIT = APP_URL . '/useredit';
    const USERS_TABLE = APP_URL . '/usertable';
}
