<?php

declare(strict_types=1);

################################
## Joël Piguet - 2021.11.23 ###
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
            error_log('Connection failed: ' . $e->getMessage() . PHP_EOL);
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
     * Retrieve the User Articles;
     * 
     * @param int $User_id User id;
     * @param int $limit The maximum number of items to be returned.
     * @param int $skip The number of result items to be skipped before including them to the result array.
     * @return array An array of Articles.
     */
    public function getUserArticles(int $User_id, int $limit, int $offset = 0): array
    {
        $preparedStatement = $this->pdo->prepare('SELECT 
            id, 
            user_id, 
            article_name, 
            location,
            comments, 
            expiration_date,
            creation_date 
        FROM Articles WHERE user_id = :uid LIMIT :lim OFFSET :off');

        $preparedStatement->bindParam(':uid', $User_id, PDO::PARAM_INT);
        $preparedStatement->bindParam(':lim', $limit, PDO::PARAM_INT);
        $preparedStatement->bindParam(':off', $offset, PDO::PARAM_INT);

        $Articles = [];

        if ($preparedStatement->execute()) {
            // fetch next as associative array until there are none to be fetched.
            while ($data = $preparedStatement->fetch()) {
                array_push($Articles, Article::fromDatabaseRow($data));
            }
        } else {
            list(,, $error) = $preparedStatement->errorInfo();
            error_log('failure to retrieve Article list from database: ' . $error . PHP_EOL);
        }

        return $Articles;
    }

    /**
     * Return a single User data by email.
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
        FROM Users WHERE email = :email');

        $preparedStatement->bindParam(':email', $email, PDO::PARAM_STR);

        if ($preparedStatement->execute()) {
            $data = $preparedStatement->fetch(PDO::FETCH_ASSOC);
            return $data ? User::fromDatabaseRow($data) : null;
        }
        list(,, $error) = $preparedStatement->errorInfo();
        error_log('failure to retrieve User from database: ' . $error . PHP_EOL);
        return null;
    }

    /**
     * Return a single User data by id.
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
        FROM Users WHERE id = :id');

        $preparedStatement->bindParam(':id', $id, PDO::PARAM_INT);


        if ($preparedStatement->execute()) {
            $data = $preparedStatement->fetch(PDO::FETCH_ASSOC);
            return $data ? User::fromDatabaseRow($data) : null;
        }
        list(,, $error) = $preparedStatement->errorInfo();
        error_log('failure to retrieve User from database: ' . $error . PHP_EOL);
        return null;
    }

    /**
     * Insert an Article to the database.
     * 
     * @param Article Article to insert.
     * @return bool True if the insert is successful.
     */
    public function insertArticle(Article $Article): bool
    {
        $preparedStatement = $this->pdo->prepare(
            'INSERT INTO articles 
            (
                user_id, 
                article_name, 
                location, 
                expiration_date
            ) 
            VALUES 
            (
                :uid, 
                :art, 
                :loc, 
                :date
            )'
        );

        $uid = $Article->getUser_id();
        $name = $Article->getArticleName();
        $location = $Article->getLocation();
        $date = $Article->getExpirationDate()->format('Y-m-d H:i:s');

        // $date = $Article->getExpirationDate();
        // $date = DateFormatter::printSQLTimestamp($Article->getExpirationDate(), true);

        error_log(strval($uid));
        error_log($name);
        error_log($location);
        error_log($date);

        $preparedStatement->bindParam(':uid', $uid, PDO::PARAM_INT);
        $preparedStatement->bindParam(':art', $name, PDO::PARAM_STR);
        $preparedStatement->bindParam(':loc', $location, PDO::PARAM_STR);
        $preparedStatement->bindParam(':date', $date, PDO::PARAM_STR);


        error_log('insert-try');
        if ($preparedStatement->execute()) {
            return true;
        }
        list(,, $error) = $preparedStatement->errorInfo();
        error_log($error . PHP_EOL);
        return false;
    }

    /**
     * Update User last_login field to now.
     * 
     * @param int $UserId ID of User.
     */
    public function updateLogTime(int $UserId): bool
    {
        $preparedStatement = $this->pdo->prepare('UPDATE Users SET last_login=:last_login WHERE id = :id');
        $now = (new \DateTime("now", new \DateTimeZone("UTC")))->format('Y.m.d H:i:s');

        $preparedStatement->bindParam(':last_login', $now, PDO::PARAM_STR);
        $preparedStatement->bindParam(':id', $UserId, PDO::PARAM_INT);

        if ($preparedStatement->execute()) {
            return true;
        }
        list(,, $error) = $preparedStatement->errorInfo();
        error_log($error . PHP_EOL);
        return false;
    }
}
