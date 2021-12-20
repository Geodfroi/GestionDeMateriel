<?php

declare(strict_types=1);

################################
## Joël Piguet - 2021.12.20 ###
##############################

namespace app\helpers\db;

use \PDO;
use Exception;

use app\constants\LogError;
use app\constants\OrderBy;
use app\constants\Settings;
use app\helpers\Logging;
use app\models\User;

/**
 * Regroup functions to interact with users table.
 */
class UserQueries
{
    private $conn;
    private bool $use_sqlite;

    /**
     * @param PDO|SQlite3 $conn Db connection.
     * @param bool $use_sqlite Set for sqlite queries instead of MySQL.
     */
    function __construct($conn, bool $use_sqlite)
    {
        $this->conn = $conn;
        $this->use_sqlite = $use_sqlite;
    }

    public function backup()
    {
        Logging::debug('user debug not implemented');
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
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        if ($stmt->execute()) {
            return true;
        }

        //list(,, $error) = $stmt->errorInfo();
        Logging::error(LogError::USER_DELETE, ['error' => $this->conn->lastErrorMsg()]);
        return false;
    }

    /**
     * Compose ORDER BY clause.
     * 
     * @param int $param OrderBy constant value.
     * @return string orderby clause.
     */
    public static function getOrderStatement(int $param): string
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

        $stmt->bindParam(':lmail', $em, PDO::PARAM_STR);
        $stmt->bindParam(':alias', $alias, PDO::PARAM_STR);
        $stmt->bindParam(':pass', $pass, PDO::PARAM_STR);
        $stmt->bindParam(':adm', $adm, PDO::PARAM_BOOL);

        $r = $stmt->execute();
        if ($r) {
            return $this->use_sqlite ? $this->conn->lastInsertRowID() : intval($this->conn->lastInsertId());
        }

        Logging::error(LogError::USER_INSERT, ['error' => $this->conn->lastErrorMsg()]);
        return 0;
    }

    /**
     * Return a single User data by alias.
     * 
     * @param int $id User alias.
     * @return ?User User class instance.
     */
    public function queryByAlias(string $alias): ?User
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

        $stmt->bindParam(':al', $alias, PDO::PARAM_STR);

        if ($stmt->execute()) {
            $data = $stmt->fetch(PDO::FETCH_ASSOC);
            return $data ? User::fromDatabaseRow($data) : null;
        }

        Logging::error(LogError::USER_QUERY, ['error' => $this->conn->lastErrorMsg()]);
        return null;
    }

    /**
     * Return a single User data by id.
     * 
     * @param int $id User id.
     * @return ?User User class instance.
     */
    public function queryById(int $id): ?User
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

        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        $r = $stmt->execute();
        if ($r) {
            if (Settings::USE_SQLITE) {
                while ($row = $r->fetchArray(SQLITE3_ASSOC)) {
                    return User::fromDatabaseRow($row);
                }
                return null;
            }
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row ? User::fromDatabaseRow($row) : null;
        }

        Logging::error(LogError::USER_QUERY, ['error' => $this->conn->lastErrorMsg()]);
        return null;
    }

    /**
     * Return a single user data by their login email.
     * 
     * @param string $login_email User login email.
     * @return ?User User class instance or null.
     */
    public function queryByEmail(string $login_email): ?User
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
        $stmt->bindParam(':email', $login_email, PDO::PARAM_STR);

        $r = $stmt->execute();
        if ($r) {
            if (SETTINGS::USE_SQLITE) {
                $row = $r->fetchArray(SQLITE3_ASSOC);
                if ($row) {
                    return User::fromDatabaseRow($row);
                }
                Logging::error('no row');

                Logging::error('numColumns: ' . $r->numColumns(), []);
                Logging::error('numColumns: ' . $r->columnName(0), []);
                Logging::error('numColumns: ' . $r->columnName(1), []);
                Logging::error('numColumns: ' . $r->columnName(2), []);
                Logging::error('numColumns: ' . $r->columnName(3), []);
                // while ($row = $r->fetchArray(SQLITE3_ASSOC)) {
                //     return User::fromDatabaseRow($row);
                // }
                return null;
            } else {
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                return $row ? User::fromDatabaseRow($row) : null;
            }
        }

        Logging::error(LogError::USER_QUERY, ['error' => $this->conn->lastErrorMsg()]);
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
        if ($stmt->execute()) {
            $r = $stmt->fetchColumn();
            return intval($r);
        }

        Logging::error(LogError::USERS_COUNT_QUERY, ['error' => $this->conn->lastErrorMsg()]);
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
        $order_statement = UserQueries::getOrderStatement($orderby);
        $filter_statement = $excludeAdmins ? ' WHERE is_admin = False' : '';

        //Only data can be bound inside $stmt, order-by params and where clause must be set before in the query string.
        $stmt = $this->conn->prepare("SELECT 
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
         $order_statement LIMIT :lim OFFSET :off");

        $stmt->bindParam(':lim', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':off', $offset, PDO::PARAM_INT);

        $r = $stmt->execute();
        if ($r) {
            $users = [];
            if (Settings::USE_SQLITE) {
                while ($row = $r->fetchArray(SQLITE3_ASSOC)) {
                    array_push($users, USER::fromDatabaseRow($row));
                }
            } else {
                // fetch next as associative array until there are none to be fetched.
                while ($data = $stmt->fetch()) {
                    array_push($users, User::fromDatabaseRow($data));
                }
            }
            return $users;
        }
        Logging::error(LogError::USERS_QUERY, ['error' => $this->conn->lastErrorMsg()]);
        return [];
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
        $stmt = $this->conn->prepare('UPDATE users SET alias=:alias WHERE id = :id');

        $stmt->bindParam(':alias', $alias, PDO::PARAM_STR);
        $stmt->bindParam(':id', $user_id, PDO::PARAM_INT);

        if ($stmt->execute()) {
            return true;
        }

        Logging::error(LogError::USER_ALIAS_UPDATE, ['error' => $this->conn->lastErrorMsg()]);
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

        $stmt->bindParam(':delay', $delay, PDO::PARAM_STR);
        $stmt->bindParam(':id', $user_id, PDO::PARAM_INT);

        if ($stmt->execute()) {
            return true;
        }

        Logging::error(LogError::USER_DELAY_UPDATE, ['error' => $this->conn->lastErrorMsg()]);
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

        $stmt->bindParam(':email', $contact_email, PDO::PARAM_STR);
        $stmt->bindParam(':id', $user_id, PDO::PARAM_INT);

        if ($stmt->execute()) {
            return true;
        }

        Logging::error(LogError::USER_CONTACT_UPDATE, ['error' => $this->conn->lastErrorMsg()]);
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
        $now = (new \DateTime("now", new \DateTimeZone("UTC")))->format('Y.m.d H:i:s');

        $stmt->bindParam(':last_login', $now, PDO::PARAM_STR);
        $stmt->bindParam(':id', $user_id, PDO::PARAM_INT);

        if ($stmt->execute()) {
            return true;
        }

        Logging::error(LogError::USER_LOGTIME_UPDATE, ['error' => $this->conn->lastErrorMsg()]);
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

        $stmt->bindParam(':pass', $new_password, PDO::PARAM_STR);
        $stmt->bindParam(':id', $user_id, PDO::PARAM_INT);

        if ($stmt->execute()) {
            return true;
        }

        Logging::error(LogError::USER_PASSWORD_UPDATE, ['error' => $this->conn->lastErrorMsg()]);
        return false;
    }
}
