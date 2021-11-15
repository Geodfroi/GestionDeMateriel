<?php

declare(strict_types=1);

################################
## Joël Piguet - 2021.11.15 ###
##############################

namespace helpers;

use \PDO;
use models\User;
use models\Article;
use PDOException;

// class QueryOrder
// {
//     const OrderByName = 0;
// }

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
     * Retrieve the user articles;
     * 
     * @param int $user_id User id;
     * @param int $limit The maximum number of items to be returned.
     * @param int $skip The number of result items to be skipped before including them to the result array.
     * @return array An array of articles.
     */
    public function getUserArticles(int $user_id, int $limit, int $offset = 0): array
    {
        $preparedStatement = $this->pdo->prepare('SELECT 
            id, 
            user_id, 
            article_name, 
            location, 
            expiration_date,
            creation_date 
        FROM articles WHERE user_id = :uid LIMIT :lim OFFSET :off');

        $preparedStatement->bindParam(':uid', $user_id, PDO::PARAM_INT);
        $preparedStatement->bindParam(':lim', $limit, PDO::PARAM_INT);
        $preparedStatement->bindParam(':off', $offset, PDO::PARAM_INT);

        $articles = [];
        try {
            if ($preparedStatement->execute()) {
                // fetch next as associative array until there are none to be fetched.
                while ($data = $preparedStatement->fetch()) {
                    array_push($articles, new Article($data));
                }
            }
        } catch (\PDOException $e) {
            echo 'failure to retrieve articles from database: ' . $e->getMessage() . PHP_EOL;
        }
        return $articles;
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

        $preparedStatement->bindParam(':email', $email, PDO::PARAM_STR);
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

        $preparedStatement->bindParam(':id', $id, PDO::PARAM_INT);

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

        $preparedStatement->bindParam(':last_login', $now, PDO::PARAM_STR);
        $preparedStatement->bindParam(':id', $userId, PDO::PARAM_INT);

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
