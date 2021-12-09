<?php

declare(strict_types=1);

################################
## Joël Piguet - 2021.12.09 ###
##############################

namespace app\helpers;

use app\constants\Session;
use app\models\User;

/**
 * Collection of static functions linked to authentification bundled into a class. 
 */
class Authenticate
{
    /**
     * Retrieve logged-in user.
     * 
     * @return ?User Logged-in User if found, otherwise null;
     */
    public static function getUser(): ?User
    {
        if (isset($_SESSION[Session::USER_ID])) {
            return Database::users()->queryById($_SESSION[Session::USER_ID]);
        }
        return null;
    }

    /**
     * Retrieve current user id.
     * 
     * @return int Current user id or -1 if no user is defined.
     */
    public static function getUserId(): int
    {
        if (isset($_SESSION[Session::USER_ID])) {
            return $_SESSION[Session::USER_ID];
        }
        return -1;
    }

    public static function isAdmin()
    {
        return isset($_SESSION[Session::IS_ADMIN]);
    }

    public static function isLoggedIn(): bool
    {
        return isset($_SESSION[Session::USER_ID]);
    }

    /**
     * Log-in user into session. The user will stay logged-in as long as the browser is open.
     * 
     * @param User $user User instance.
     */
    public static function login(User $user)
    {
        $_SESSION[Session::USER_ID] = $user->getId();

        // USER_ID and ADMIN_ID are separate to allow admin to log-in as a different user and keep admin privileges.
        if ($user->isAdmin()) {
            $_SESSION[Session::IS_ADMIN] = true;
        }

        Database::users()->updateLogTime($user->getId());
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
            // setcookie(COOKIE_NAME, '-1',  -1000); // reset cookie with negative life expectancy will delete it.
        }
    }
}

    // /**
    //  * Log-in to a user account as an admin.
    //  */
    // public static function login_as(int $id)
    // {
    //     $_SESSION[Session::USER_ID] = $id;
    // }