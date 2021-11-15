<?php

declare(strict_types=1);

################################
## Joël Piguet - 2021.11.15 ###
##############################

namespace helpers;

use models\User;

const USER_ID = 'user_id';

/**
 * Collection of static functions linked to authentification bundled into a class. 
 */
class Authenticate
{
    /**
     * Log-in user into session. The user will stay logged-in as long as the browser is open.
     * 
     * @param User $user User instance.
     */
    public static function login(User $user)
    {
        $_SESSION[USER_ID] = $user->getId();
        Database::getInstance()->updateLogTime($user->getId());
    }

    public static function isLoggedIn(): bool
    {
        return isset($_SESSION[USER_ID]);
    }

    /**
     * Retrieve logged-in user.
     * 
     * @return ?User Logged-in User if found, otherwise null;
     */
    public static function getUser(): ?User
    {
        if (isset($_SESSION[USER_ID])) {
            return Database::getInstance()->getUserById($_SESSION[USER_ID]);
        }
        return null;
    }
}


// use DateTime;
// use Models\User;

// class Auth
// {
//     public static function getUser(): ?User
//     {
//         if (self::userIsAuthenticated()) {
//             return Database::instance()->getUserById((int)$_SESSION['userid']);
//         }
//         return null;
//     }

//     public static function logout()
//     {
//         if (session_status() === PHP_SESSION_ACTIVE) {
//             $_SESSION = []; // clear the stored values in current call $_SESSION
//             session_regenerate_id(true);
//             session_destroy();
//         }
//     }
// }
