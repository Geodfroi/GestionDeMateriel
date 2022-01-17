<?php

declare(strict_types=1);

################################
## Joël Piguet - 2022.01.09 ###
##############################

namespace app\helpers\db;

use Exception;
use SQLite3;

use app\constants\LogError;
use app\constants\OrderBy;
use app\helpers\Logging;
use app\models\User;

/**
 * Regroup functions to interact with users table.
 */
class UserQueries extends Queries
{
    /**
     * @param SQlite3 $backup_conn Db backup connection.
     * @return True if backup is successful.
     */
    public function backup(SQlite3 $backup_conn): bool
    {
        $query_stmt = $this->conn->prepare("SELECT * FROM users");
        $r = $query_stmt->execute();

        while ($row = $this->fetchRow($r, $query_stmt)) {
            $id  = (int)($row['id'] ?? 0);
            $alias = (string)($row['alias'] ?? '');
            $contact_delay = (string)($row['contact_delay'] ?? '');
            $contact_email = (string)($row['contact_email'] ?? '');
            $creation_date = (string)($row['creation_date'] ?? '');
            $login_email = (string)($row['login_email'] ?? '');
            $is_admin  = (bool)($row['is_admin'] ?? 0);
            $last_login = (string)($row['last_login'] ?? '');
            $password = (string)($row['password'] ?? '');

            $insert_stmt = $backup_conn->prepare('INSERT INTO users 
            (   
                id,
                alias, 
                contact_delay, 
                contact_email, 
                creation_date,
                login_email,
                is_admin,
                last_login,
                password
            ) 
            VALUES 
            (
                :id,
                :alias, 
                :contact_delay, 
                :contact_email, 
                :creation_date,
                :login_email,
                :is_admin,
                :last_login,
                :password
            )');

            $insert_stmt->bindParam(':id', $id, SQLITE3_INTEGER);
            $insert_stmt->bindParam(':alias', $alias, SQLITE3_TEXT);
            $insert_stmt->bindParam(':contact_delay', $contact_delay, SQLITE3_TEXT);
            $insert_stmt->bindParam(':contact_email', $contact_email, SQLITE3_TEXT);
            $insert_stmt->bindParam(':creation_date', $creation_date, SQLITE3_TEXT);
            $insert_stmt->bindParam(':login_email', $login_email, SQLITE3_TEXT);
            $insert_stmt->bindParam(':is_admin', $is_admin, SQLITE3_INTEGER);
            $insert_stmt->bindParam(':last_login', $last_login, SQLITE3_TEXT);
            $insert_stmt->bindParam(':password', $password, SQLITE3_TEXT);

            if (!$insert_stmt->execute()) {
                Logging::error('failure to insert user in backup db', ['user' => $login_email]);
                return false;
            };
        }
        return true;
    }

    /**
     * Delete the user from db by id.
     * 
     * @param int $id User id.
     * @return bool True if the delete is successful.
     */
    public function delete($id): bool
    {
        $stmt = $this->conn->prepare('DELETE FROM users WHERE id = :id');
        $stmt->bindParam(':id', $id, $this->data_types['int']);

        if ($stmt->execute()) {
            return true;
        }

        Logging::error(LogError::USER_DELETE, [
            'id' => $id,
            'error' => $this->error($stmt)
        ]);
        return false;
    }

    /**
     * Compose ORDER BY clause.
     * 
     * @param string $param OrderBy constant value.
     * @return string orderby clause.
     */
    public static function getOrderStatement(string $param): string
    {
        switch ($param) {
                //simply by email which are unique.
            case OrderBy::EMAIL_ASC:
                return 'ORDER BY login_email ASC';
            case OrderBy::EMAIL_DESC:
                return 'ORDER BY login_email DESC';

                // by creation date, then email.
            case OrderBy::CREATED_ASC:
                return 'ORDER BY creation_date ASC, login_email ASC';
            case OrderBy::CREATED_DESC:
                return 'ORDER BY creation_date DESC, login_email ASC';

                // by last login date.
            case OrderBy::LOGIN_ASC:
                return 'ORDER BY last_login ASC';
            case OrderBy::LOGIN_DESC:
                return 'ORDER BY last_login DESC';
            default:
                break;
        }
        throw new Exception("printOrderStatement: invalid [$param)] parameter");
    }

    /**     
     * Insert a User into the database.
     * 
     * @param User $user User to insert.
     * @return int ID of inserted row or 0 if it fails.
     */
    public function insert(User $user): int
    {
        $stmt = $this->conn->prepare(
            'INSERT INTO users 
            (
                login_email,
                alias,
                password,
                is_admin
            ) 
            VALUES 
            (
                :lmail, 
                :alias,
                :pass, 
                :adm
            )'
        );

        $em = $user->getLoginEmail();
        $alias = $user->getAlias();
        $pass = $user->getPassword();
        $adm = $user->isAdmin();

        $stmt->bindParam(':lmail', $em, $this->data_types['str']);
        $stmt->bindParam(':alias', $alias, $this->data_types['str']);
        $stmt->bindParam(':pass', $pass, $this->data_types['str']);
        $stmt->bindParam(':adm', $adm, $this->data_types['bool']);

        $r = $stmt->execute();
        if ($r) {
            $id = $this->rowId();
            $user->setId($id);
            return $id;
        }

        Logging::error(LogError::USER_INSERT, ['error' => $this->error($stmt)]);
        return 0;
    }

    /**
     * Return a single User data by alias.
     * 
     * @param int $id User alias.
     * @param bool suppress error logging
     * @return ?User User class instance.
     */
    public function queryByAlias(string $alias, $suppress_error = false): ?User
    {
        $stmt = $this->conn->prepare('SELECT 
            id, 
            alias,
            contact_email,
            contact_delay,
            login_email, 
            password, 
            creation_date, 
            last_login,
            is_admin 
        FROM users WHERE alias = :al');

        $stmt->bindParam(':al', $alias, $this->data_types['str']);

        $r = $stmt->execute();
        if ($r) {
            $row = $this->fetchRow($r, $stmt);
            if ($row) {
                return User::fromDatabaseRow($row);
            }
            if (!$suppress_error) {
                Logging::error(LogError::USER_QUERY, ['alias' => $alias]);
            }
            return null;
        }
        if (!$suppress_error) {
            Logging::error(LogError::USER_QUERY, [
                'alias' => $alias,
                'error' => $this->error($stmt)
            ]);
        }
        return null;
    }

    /**
     * Return a single User data by id.
     * 
     * @param int $id User id.
     * @param bool suppress error logging
     * @return ?User User class instance.
     */
    public function queryById(int $id, $suppress_error = false): ?User
    {
        $stmt = $this->conn->prepare('SELECT 
            id, 
            alias,
            contact_email,
            contact_delay,
            login_email, 
            password, 
            creation_date, 
            last_login,
            is_admin 
        FROM users WHERE id = :id');

        $stmt->bindParam(':id', $id, $this->data_types['int']);

        $r = $stmt->execute();
        if ($r) {
            $row = $this->fetchRow($r, $stmt);
            if ($row) {
                return User::fromDatabaseRow($row);
            }
            if (!$suppress_error) {
                Logging::error(LogError::USER_QUERY, ['id' => $id]);
            }

            return null;
        }
        if (!$suppress_error) {
            Logging::error(LogError::USER_QUERY, [
                'id' => $id,
                'error' => $this->error($stmt)
            ]);
        }

        return null;
    }

    /**
     * Return a single user data by their login email.
     * 
     * @param string $login_email User login email.
     *     * @param bool suppress error logging
     * @return ?User User class instance or null.
     */
    public function queryByEmail(string $login_email, $suppress_error = false): ?User
    {
        $query = 'SELECT 
            id, 
            alias,
            contact_email,
            contact_delay,
            login_email, 
            password, 
            creation_date, 
            last_login,
            is_admin 
        FROM users WHERE login_email = :email';

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $login_email, $this->data_types['str']);

        $r = $stmt->execute();
        if ($r) {
            $row = $this->fetchRow($r, $stmt);
            if ($row) {
                return User::fromDatabaseRow($row);
            }
            if (!$suppress_error) {
                Logging::error(LogError::USER_QUERY, ['login_email' => $login_email,]);
            }
            return null;
        }
        if (!$suppress_error) {
            Logging::error(LogError::USER_QUERY, [
                'login_email' => $login_email,
                'error' => $this->error($stmt)
            ]);
        }

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

        $stmt = $this->conn->prepare($query);
        $r = $stmt->execute();
        if ($r) {
            return $this->count($r, $stmt);
        }

        Logging::error(LogError::USERS_COUNT_QUERY, [
            'exclude_admin' => $excludeAdmins,
            'error' => $this->error($stmt)
        ]);
        return -1;
    }

    /**
     * Retrieve user array from database.
     * 
     * @param int $limit The maximum number of users to be returned.
     * @param int $offset The number of result users to be skipped before including them to the result array.
     * @param string $orderby Order parameter. Use OrderBy constants as parameter.
     * @param bool $excludeAdmins Count only common users.
     * @return array Array of users.
     */
    public function queryAll(int $limit = PHP_INT_MAX, int $offset = 0, string $orderby = OrderBy::EMAIL_ASC, bool $excludeAdmins = false): array
    {
        $order_statement = UserQueries::getOrderStatement($orderby);
        $filter_statement = $excludeAdmins ? ' WHERE is_admin = False' : '';

        //Only data can be bound inside $stmt, order-by params and where clause must be set before in the query string.
        $query = "SELECT 
             id, 
             alias,
             contact_email,
             contact_delay,
             login_email, 
             password, 
             creation_date,
             last_login, 
             is_admin
         FROM users 
         $filter_statement 
         $order_statement LIMIT :lim OFFSET :off";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':lim', $limit, $this->data_types['int']);
        $stmt->bindParam(':off', $offset, $this->data_types['int']);

        $r = $stmt->execute();
        if ($r) {
            $users = [];
            // fetch next as associative array until there are none to be fetched.
            while ($row = $this->fetchRow($r, $stmt)) {
                array_push($users, USER::fromDatabaseRow($row));
            }
            return $users;
        }

        Logging::error(LogError::USERS_QUERY, ['error' => $this->error($stmt)]);
        return [];
    }

    /**
     * Update user alias.
     * 
     * @param int $user_id User id.
     * @param string $alias Updated alias for user.
     * @return bool True is update is successful.
     */
    public function updateAlias(int $user_id, string $alias): bool
    {
        if (!$alias) {
            return false;
        }

        $stmt = $this->conn->prepare('UPDATE users SET alias=:alias WHERE id = :id');

        $stmt->bindParam(':alias', $alias, $this->data_types['str']);
        $stmt->bindParam(':id', $user_id, $this->data_types['int']);

        if ($stmt->execute()) {
            return true;
        }

        Logging::error(LogError::USER_ALIAS_UPDATE, ['error' => $this->error($stmt)]);
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
        $stmt = $this->conn->prepare('UPDATE users SET contact_delay=:delay WHERE id = :id');

        $stmt->bindParam(':delay', $delay, $this->data_types['str']);
        $stmt->bindParam(':id', $user_id, $this->data_types['int']);

        if ($stmt->execute()) {
            return true;
        }

        Logging::error(LogError::USER_DELAY_UPDATE, ['error' => $this->error($stmt)]);
        return false;
    }

    /**
     * Update user contact adress.
     * 
     * @param int $user_id User id.
     * @param string $contact_email Updated contact email.
     * @return bool True is update is successful.
     */
    public function updateContactEmail(int $user_id, string $contact_email): bool
    {
        $stmt = $this->conn->prepare('UPDATE users SET contact_email=:email WHERE id = :id');

        $stmt->bindParam(':email', $contact_email, $this->data_types['str']);
        $stmt->bindParam(':id', $user_id, $this->data_types['int']);

        if ($stmt->execute()) {
            return true;
        }

        Logging::error(LogError::USER_CONTACT_UPDATE, ['error' => $this->error($stmt)]);
        return false;
    }

    /**
     * Update User last_login field to now.
     * 
     * @param int $user_id ID of User.
     */
    public function updateLogTime(int $user_id): bool
    {
        $stmt = $this->conn->prepare('UPDATE users SET last_login=:last_login WHERE id = :id');
        $now = (new \DateTime("now", new \DateTimeZone("UTC")))->format('Y-m-d H:i:s');

        $stmt->bindParam(':last_login', $now, $this->data_types['str']);
        $stmt->bindParam(':id', $user_id, $this->data_types['int']);

        if ($stmt->execute()) {
            return true;
        }

        Logging::error(LogError::USER_LOGTIME_UPDATE, ['error' => $this->error($stmt)]);
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
        $stmt = $this->conn->prepare('UPDATE users SET password=:pass WHERE id = :id');

        $stmt->bindParam(':pass', $new_password, $this->data_types['str']);
        $stmt->bindParam(':id', $user_id, $this->data_types['int']);

        if ($stmt->execute()) {
            return true;
        }

        Logging::error(LogError::USER_PASSWORD_UPDATE, ['error' => $this->error($stmt)]);
        return false;
    }
}
