<?php

declare(strict_types=1);

################################
## Joël Piguet - 2021.11.15 ###
##############################

namespace helpers;

use Exception;
use \PDO;
use models\User;
use PDOException;

/**
 * Database class accessible throughout the application by calling it'ss get_instance() method. 
 */
class Database
{
    private $pdo;

    /**
     * Initialise connection to the MySQL inside the constructor dunder method.
     * ! put the connection parameters (including admin password to db) in a settings.php file which is not shared on github.
     */
    function __construct()
    {
        try {
            $dsn = 'mysql:host=' . HOST . ';port=' . PORT . ';dbname=' . DB_NAME . ';charset=utf8mb4';
            $options = [
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ];
            $this->pdo = new PDO($dsn, ADMIN_ID, ADMIN_PASSWORD, $options);
        } catch (PDOException $e) {
            echo 'Connection failed: ' . $e->getMessage() . PHP_EOL;
        }
    }

    /**
     * Simpleton pattern insures there is only one instance of Database class in the whole application
     * 
     * @return Database Always return the same instance of Database class.
     */
    public static function getInstance()
    {
        static $instance;
        if (is_null($instance)) {
            $instance = new static();
        }
        return $instance;
    }

    public function getEntries(int $userId, int $max_count, $offset)
    {
        throw new Exception('Not implemented');
    }

    /**
     * Return a single user data by email.
     * 
     * @param string $email User email.
     * @return ?User User class instance.
     */
    public function getUserByEmail(string $email): ?User
    {
        $preparedStatement = $this->pdo->prepare('SELECT 
            id, 
            email, 
            password, 
            creation_date, 
            last_login,
            is_admin 
        FROM users WHERE email = :email');

        $preparedStatement->bindParam(':email', $email);
        try {
            if ($preparedStatement->execute()) {
                $data = $preparedStatement->fetch(PDO::FETCH_ASSOC);
                return $data ? new User($data) : null;
            }
        } catch (\PDOException $e) {
            echo 'failure to retrieve user from database: ' . $e->getMessage() . PHP_EOL;
        }

        return null;
    }

    /**
     * Return a single user data by id.
     * 
     * @param int $id User id.
     * @return ?User User class instance.
     */
    public function getUserById(int $id): ?User
    {
        $preparedStatement = $this->pdo->prepare('SELECT 
            id, 
            email, 
            password, 
            creation_date, 
            last_login,
            is_admin 
        FROM users WHERE id = :id');

        $preparedStatement->bindParam(':id', $id);

        try {
            if ($preparedStatement->execute()) {
                $data = $preparedStatement->fetch(PDO::FETCH_ASSOC);
                return $data ? new User($data) : null;
            }
        } catch (\PDOException $e) {
            echo 'failure to retrieve user from database: ' . $e->getMessage() . PHP_EOL;
        }
        return null;
    }


    /**
     * Update user last_login field to now.
     * 
     * @param int $userId ID of user.
     */
    public function updateLogTime(int $userId): bool
    {
        $preparedStatement = $this->pdo->prepare('UPDATE users SET last_login=:last_login WHERE id = :id');
        $now = (new \DateTime("now", new \DateTimeZone("UTC")))->format('Y.m.d H:i:s');

        $preparedStatement->bindParam(':last_login', $now);
        $preparedStatement->bindParam(':id', $userId);

        try {
            if ($preparedStatement->execute()) {
                return true;
            }
        } catch (\PDOException $e) {
            echo 'failure to update user last_login: ' . $e->getMessage() . PHP_EOL;
        }
        return false;
    }
}
