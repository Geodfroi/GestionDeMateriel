<?php

declare(strict_types=1);

################################
## Joël Piguet - 2021.11.16 ###
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

    /**
     * Remove credentials from _SESSION and cookies.
     */
    public static function logout()
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            $_SESSION = []; // clear the stored values in current $_SESSION global variable.
            session_regenerate_id(true); // send header to browser to remove id cookie.
            session_destroy();
        }
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
