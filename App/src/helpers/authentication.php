<?php

declare(strict_types=1);

################################
## Joël Piguet - 2021.11.14 ###
##############################

namespace helpers;

use Exception;

/**
 * Collection of static functions linked to authentification bundled into a class.
 */
class Authenticate
{
    public static function login(int $id)
    {
        throw new Exception('not implemented');
    }

    public static function isLoggedIn(): bool
    {
        return isset($_SESSION['user_id']);
    }
}


// use DateTime;
// use Models\User;

// class Auth
// {
//     public static function getLastLogin(): DateTime
//     {
//         return DateTime::createFromFormat('U', (string)($_SESSION['loginTime'] ?? ''));
//     }

//     public static function getUser(): ?User
//     {
//         if (self::userIsAuthenticated()) {
//             return Database::instance()->getUserById((int)$_SESSION['userid']);
//         }
//         return null;
//     }

//     public static function authenticate(int $id)
//     {
//         $_SESSION['userid'] = $id;
//         $_SESSION['loginTime'] = time();
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
