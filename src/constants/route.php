<?php

declare(strict_types=1);

################################
## Joël Piguet - 2021.12.07 ###
##############################

namespace app\constants;

class Route
{
    // routes
    const ADMIN = '/admin';
    const ART_TABLE = '/articlesTable';
    const ART_EDIT = '/articleEdit';
    const CONTACT = '/contact';
    const LOCAL_PRESETS = '/location_presets';
    const LOGIN = '/login';
    const LOGOUT = '/login?logout=true';
    const HOME = '/';
    const PROFILE = '/profile';
    const USER_EDIT = '/userEdit';
    const USERS_TABLE = '/usersTable';
}
