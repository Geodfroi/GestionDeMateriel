<?php

declare(strict_types=1);

################################
## Joël Piguet - 2021.11.14 ###
##############################

namespace helpers;

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

    /**
     * Return a single user data by email.
     * 
     * @param string $email user email.
     * @return ?User user class instance.
     */
    public function getUserByEmail(string $email): ?User
    {
        $preparedStatement = $this->pdo->prepare('SELECT 
            id, 
            email, 
            password, 
            creation_date, 
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
}
