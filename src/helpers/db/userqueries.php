<?php

declare(strict_types=1);

################################
## Joël Piguet - 2021.12.07 ###
##############################

namespace app\helpers\db;

use \PDO;

use app\constants\Error;
use app\constants\OrderBy;
use app\models\User;

/**
 * Regroup functions to interact with users table.
 */
class UserQueries
{
    private PDO $pdo;

    function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Delete the user from db by id.
     * 
     * @param int $id User id.
     * @return bool True if the delete is successful.
     */
    public function delete($id): bool
    {
        $preparedStatement = $this->pdo->prepare('DELETE FROM users WHERE id = :id');
        $preparedStatement->bindParam(':id', $id, PDO::PARAM_INT);

        if ($preparedStatement->execute()) {
            return true;
        }
        list(,, $error) = $preparedStatement->errorInfo();
        error_log(Error::USER_DELETE . $error . PHP_EOL);
        return false;
    }

    /**
     * Return orderby query element.
     * 
     * @param int $param OrderBy Const value.
     * @return Array orderby string parameters.
     */
    public static function getOrderStatement(int $param): string
    {
        switch ($param) {

                // case OrderBy::CREATED_ASC:
                //     return 'creation_date ASC';
                // case OrderBy::CREATED_DESC:
                //     return 'creation_date DESC';
                // case OrderBy::DATE_ASC:
                //     return 'expiration_date ASC';
                // case OrderBy::DATE_DESC:
                //     return 'expiration_date DESC';
                // case OrderBy::EMAIL_ASC:
                //     return 'email ASC';
                // case OrderBy::EMAIL_DESC:
                //     return 'email DESC';
                // case OrderBy::LOCATION_ASC:
                //     return 'location ASC';
                // case OrderBy::LOCATION_DESC:
                //     return 'location DESC';
                // case OrderBy::LOGIN_ASC:
                //     return 'last_login ASC';
                // case OrderBy::LOGIN_DESC:
                //     return 'last_login DESC';
                // case OrderBy::NAME_ASC:
                //     return 'article_name ASC';
                // case OrderBy::NAME_DESC:
                //     return 'article_name DESC';
                // case OrderBy::OWNED_BY:
                //     return 'user_id ASC';
            default:
                return '';
        }
    }

    /**     
     * Insert a User into the database.
     * 
     * @param User $user User to insert.
     * @return bool True if the insert is successful.
     */
    public function insert(User $user): bool
    {
        $preparedStatement = $this->pdo->prepare(
            'INSERT INTO users 
            (
                email,
                password,
                is_admin
            ) 
            VALUES 
            (
                :em, 
                :pass, 
                :adm
            )'
        );

        $em = $user->getEmail();
        $pass = $user->getPassword();
        $adm = $user->isAdmin();

        $preparedStatement->bindParam(':em', $em, PDO::PARAM_STR);
        $preparedStatement->bindParam(':pass', $pass, PDO::PARAM_STR);
        $preparedStatement->bindParam(':adm', $adm, PDO::PARAM_BOOL);

        if ($preparedStatement->execute()) {
            return true;
        }
        list(,, $error) = $preparedStatement->errorInfo();
        error_log(Error::USER_INSERT . $error . PHP_EOL);
        return false;
    }

    /**
     * Return a single User data by id.
     * 
     * @param int $id User id.
     * @return ?User User class instance.
     */
    public function queryById(int $id): ?User
    {
        $preparedStatement = $this->pdo->prepare('SELECT 
            id, 
            alias,
            contact_email,
            contact_delay,
            email, 
            password, 
            creation_date, 
            last_login,
            is_admin 
        FROM Users WHERE id = :id');

        $preparedStatement->bindParam(':id', $id, PDO::PARAM_INT);


        if ($preparedStatement->execute()) {
            $data = $preparedStatement->fetch(PDO::FETCH_ASSOC);
            return $data ? User::fromDatabaseRow($data) : null;
        }
        list(,, $error) = $preparedStatement->errorInfo();
        error_log(Error::USER_QUERY . $error . PHP_EOL);
        return null;
    }

    /**
     * Return a single User data by email.
     * 
     * @param string $email User email.
     * @return ?User User class instance.
     */
    public function queryByEmail(string $email): ?User
    {
        $preparedStatement = $this->pdo->prepare('SELECT 
            id, 
            alias,
            contact_email,
            contact_delay,
            email, 
            password, 
            creation_date, 
            last_login,
            is_admin 
        FROM Users WHERE email = :email');

        $preparedStatement->bindParam(':email', $email, PDO::PARAM_STR);

        if ($preparedStatement->execute()) {
            $data = $preparedStatement->fetch(PDO::FETCH_ASSOC);
            return $data ? User::fromDatabaseRow($data) : null;
        }
        list(,, $error) = $preparedStatement->errorInfo();
        error_log(Error::USER_QUERY . $error . PHP_EOL);
        return null;
    }

    /**
     * Count users in database user table.
     * 
     * @param bool $excludeAdmins Count only common users.
     */
    public function queryCount(bool $excludeAdmins = true)
    {
        $query = 'SELECT 
            COUNT(*)
            FROM users';
        if ($excludeAdmins) {
            $query .= " WHERE is_admin = False";
        }

        $preparedStatement = $this->pdo->prepare($query);
        if ($preparedStatement->execute()) {
            $r = $preparedStatement->fetchColumn();
            return intval($r);
        }

        list(,, $error) = $preparedStatement->errorInfo();
        error_log(Error::USERS_COUNT_QUERY . $error . PHP_EOL);
        return -1;
    }

    /**
     * Retrieve user array from database.
     * 
     * @param bool $excludeAdmins Count only common users.
     * @param int $limit The maximum number of users to be returned.
     * @param int $offset The number of result users to be skipped before including them to the result array.
     * @param int $orderby Order parameter. Use OrderBy constants as parameter.
     * @return array Array of users.
     */
    public function queryAll(int $limit = PHP_INT_MAX, int $offset = 0, int $orderby = OrderBy::EMAIL_ASC, bool $excludeAdmins = false): array
    {
        $order = UserQueries::getOrderStatement($orderby);
        $filter_admin = $excludeAdmins ? ' WHERE is_admin = False' : '';

        // //Only data can be bound inside $preparedStatement, order-by params and where clause must be set before in the query string.
        $query_str = "SELECT 
             id, 
             alias,
             contact_email,
             contact_delay,
             email, 
             password, 
             creation_date,
             last_login, 
             is_admin
         FROM users 
         $filter_admin 
         ORDER BY $order LIMIT :lim OFFSET :off";

        $preparedStatement = $this->pdo->prepare($query_str);
        $preparedStatement->bindParam(':lim', $limit, PDO::PARAM_INT);
        $preparedStatement->bindParam(':off', $offset, PDO::PARAM_INT);

        $users = [];

        if ($preparedStatement->execute()) {
            // fetch next as associative array until there are none to be fetched.
            while ($data = $preparedStatement->fetch()) {
                array_push($users, User::fromDatabaseRow($data));
            }
        } else {
            list(,, $error) = $preparedStatement->errorInfo();
            error_log(Error::USERS_QUERY  . $error . PHP_EOL);
        }

        return $users;
    }

    /**
     * Update user alias.
     * 
     * @param int $user_id User id.
     * @param string $alias Updated alias for user.
     * @return bool True is update is successful.
     */
    public function updateAlias(int $user_id, string $alias)
    {
        $preparedStatement = $this->pdo->prepare('UPDATE users SET alias=:alias WHERE id = :id');

        $preparedStatement->bindParam(':alias', $alias, PDO::PARAM_STR);
        $preparedStatement->bindParam(':id', $user_id, PDO::PARAM_INT);

        if ($preparedStatement->execute()) {
            return true;
        }
        list(,, $error) = $preparedStatement->errorInfo();
        error_log(Error::USER_ALIAS_UPDATE . $error . PHP_EOL);
        return false;
    }

    /**
     * Update user contact delay.
     * 
     * @param int $user_id User id.
     * @param string $delay Updated contact delay as a string of concatenated numbers
     * @return bool True is update is successful.
     */
    public function updateContactDelay(int $user_id, string $delay): bool
    {
        $preparedStatement = $this->pdo->prepare('UPDATE users SET contact_delay=:delay WHERE id = :id');

        $preparedStatement->bindParam(':delay', $delay, PDO::PARAM_STR);
        $preparedStatement->bindParam(':id', $user_id, PDO::PARAM_INT);

        if ($preparedStatement->execute()) {
            return true;
        }
        list(,, $error) = $preparedStatement->errorInfo();
        error_log(Error::USER_DELAY_UPDATE . $error . PHP_EOL);
        return false;
    }

    /**
     * Update user contact adress.
     * 
     * @param int $user_id User id.
     * @param string $email Updated contact email.
     * @return bool True is update is successful.
     */
    public function updateContactEmail(int $user_id, string $email): bool
    {
        $preparedStatement = $this->pdo->prepare('UPDATE users SET contact_email=:email WHERE id = :id');

        $preparedStatement->bindParam(':email', $email, PDO::PARAM_STR);
        $preparedStatement->bindParam(':id', $user_id, PDO::PARAM_INT);

        if ($preparedStatement->execute()) {
            return true;
        }
        list(,, $error) = $preparedStatement->errorInfo();
        error_log(Error::USER_CONTACT_UPDATE . $error . PHP_EOL);
        return false;
    }

    /**
     * Update User last_login field to now.
     * 
     * @param int $user_id ID of User.
     */
    public function updateLogTime(int $user_id): bool
    {
        $preparedStatement = $this->pdo->prepare('UPDATE users SET last_login=:last_login WHERE id = :id');
        $now = (new \DateTime("now", new \DateTimeZone("UTC")))->format('Y.m.d H:i:s');

        $preparedStatement->bindParam(':last_login', $now, PDO::PARAM_STR);
        $preparedStatement->bindParam(':id', $user_id, PDO::PARAM_INT);

        if ($preparedStatement->execute()) {
            return true;
        }
        list(,, $error) = $preparedStatement->errorInfo();
        error_log(Error::USER_LOGTIME_UPDATE . $error . PHP_EOL);
        return false;
    }

    /**
     * Update user password.
     * 
     * @param int $user_id User id.
     * @param string $new_password Updated password.
     * @return bool True is update is successful.
     */
    public function updatePassword(int $user_id, string $new_password): bool
    {
        $preparedStatement = $this->pdo->prepare('UPDATE users SET password=:pass WHERE id = :id');

        $preparedStatement->bindParam(':pass', $new_password, PDO::PARAM_STR);
        $preparedStatement->bindParam(':id', $user_id, PDO::PARAM_INT);

        if ($preparedStatement->execute()) {
            return true;
        }
        list(,, $error) = $preparedStatement->errorInfo();
        error_log(Error::USER_PASSWORD_UPDATE  . $error . PHP_EOL);
        return false;
    }
}
