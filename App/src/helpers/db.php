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
     * Delete the article from db by id.
     * 
     * @param int $id Article's id.
     * @return bool True if the delete is successful.
     */
    public function deleteArticleByID(int $id): bool
    {
        $preparedStatement = $this->pdo->prepare('DELETE FROM articles WHERE id = :id');
        $preparedStatement->bindParam(':id', $id, PDO::PARAM_INT);

        if ($preparedStatement->execute()) {
            return true;
        }
        list(,, $error) = $preparedStatement->errorInfo();
        error_log('failure to delete article from database: ' . $error . PHP_EOL);
        return false;
    }

    /**
     * Retrive a single Article by it's id.
     * 
     * @param int $id Article's id.
     * @return Article|null
     */
    public function getArticleById(int $id): ?Article
    {
        $preparedStatement = $this->pdo->prepare('SELECT 
            id, 
            user_id, 
            article_name, 
            location,
            comments, 
            expiration_date,
            creation_date 
        FROM Articles WHERE id = :id');

        $preparedStatement->bindParam(':id', $id, PDO::PARAM_INT);

        if ($preparedStatement->execute()) {

            $data = $preparedStatement->fetch(); // retrieve only first row found; fine since id is unique.
            if ($data) {
                return Article::fromDatabaseRow($data);
            } else {
                error_log('failure to retrieve Article from database: no row with index [' . $id . '] was found.');
            }
        } else {
            list(,, $error) = $preparedStatement->errorInfo();
            error_log('failure to retrieve Article from database: ' . $error . PHP_EOL);
        }

        return null;
    }

    /**
     * Retrieve the User Articles;
     * 
     * @param int $User_id User id;
     * @param int $limit The maximum number of items to be returned.
     * @param int $skip The number of result items to be skipped before including them to the result array.
     * @param int $orderby Order parameter. Use ArticleOrder constants as parameter.
     * @return array An array of Articles.
     */
    public function getUserArticles(int $user_id, int $limit, int $offset = 0, int $orderby = ArticleOrder::ORDER_BY_DATE_DESC): array
    {
        [$order_column, $order_dir] = ArticleOrder::getOrderParameters($orderby);

        //Only data can be bound inside $preparedStatement, order-by params must be set before in the query string.
        $query_str = sprintf('SELECT 
            id, 
            user_id, 
            article_name, 
            location,
            comments, 
            expiration_date,
            creation_date 
        FROM articles WHERE user_id = :uid 
        ORDER BY %s %s LIMIT :lim OFFSET :off', $order_column, $order_dir);

        $preparedStatement = $this->pdo->prepare($query_str);

        $preparedStatement->bindParam(':uid', $user_id, PDO::PARAM_INT);
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
     * @param Article $article Article to insert.
     * @return bool True if the insert is successful.
     */
    public function insertArticle(Article $article): bool
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

        $uid = $article->getUserId();
        $name = $article->getArticleName();
        $location = $article->getLocation();
        $date = $article->getExpirationDate()->format('Y-m-d H:i:s');

        $preparedStatement->bindParam(':uid', $uid, PDO::PARAM_INT);
        $preparedStatement->bindParam(':art', $name, PDO::PARAM_STR);
        $preparedStatement->bindParam(':loc', $location, PDO::PARAM_STR);
        $preparedStatement->bindParam(':date', $date, PDO::PARAM_STR);

        if ($preparedStatement->execute()) {
            return true;
        }
        list(,, $error) = $preparedStatement->errorInfo();
        error_log('failure to insert article: ' . $error . PHP_EOL);
        return false;
    }

    /**
     * Update article in database.
     * 
     * @param Article Article to be updated.
     * @return bool True if update is successful.
     */
    public function updateArticle(Article $article): bool
    {
        $preparedStatement = $this->pdo->prepare('UPDATE articles SET
            article_name = :name,
            location = :loc,
            expiration_date = :date,
            comments = :com
        WHERE id = :id');

        $name = $article->getArticleName();
        $location = $article->getLocation();
        $date = $article->getExpirationDate()->format('Y-m-d H:i:s');
        $comments = $article->getComments();
        $id = $article->getId();

        $preparedStatement->bindParam(':name', $name, PDO::PARAM_STR);
        $preparedStatement->bindParam(':loc', $location, PDO::PARAM_STR);
        $preparedStatement->bindParam(':date', $date, PDO::PARAM_STR);
        $preparedStatement->bindParam(':com', $comments, PDO::PARAM_STR);
        $preparedStatement->bindParam(':id', $id, PDO::PARAM_INT);

        if ($preparedStatement->execute()) {
            return true;
        }
        list(,, $error) = $preparedStatement->errorInfo();
        error_log('failure to update article: ' . $error . PHP_EOL);
        return false;
    }

    /**
     * Update User last_login field to now.
     * 
     * @param int $UserId ID of User.
     */
    public function updateLogTime(int $UserId): bool
    {
        $preparedStatement = $this->pdo->prepare('UPDATE users SET last_login=:last_login WHERE id = :id');
        $now = (new \DateTime("now", new \DateTimeZone("UTC")))->format('Y.m.d H:i:s');

        $preparedStatement->bindParam(':last_login', $now, PDO::PARAM_STR);
        $preparedStatement->bindParam(':id', $UserId, PDO::PARAM_INT);

        if ($preparedStatement->execute()) {
            return true;
        }
        list(,, $error) = $preparedStatement->errorInfo();
        error_log('failure to update user log time' . $error . PHP_EOL);
        return false;
    }
}

/**
 * Order enum implemented as const of a class.
 */
class ArticleOrder
{
    const ORDER_BY_NAME_DESC = 0;
    const ORDER_BY_NAME_ASC = 1;
    const ORDER_BY_LOCATION_DESC = 2;
    const ORDER_BY_LOCATION_ASC = 3;
    const ORDER_BY_DATE_DESC = 4;
    const ORDER_BY_DATE_ASC = 5;

    /**
     * Return orderby query element.
     * 
     * @param int $const ArticleOrder const value.
     * @return Array orderby string parameters.
     */
    public static function getOrderParameters(int $const): array
    {
        switch ($const) {
            case ArticleOrder::ORDER_BY_NAME_DESC:
                return ['article_name', 'DESC'];
            case ArticleOrder::ORDER_BY_NAME_ASC:
                return ['article_name', 'ASC'];
            case ArticleOrder::ORDER_BY_LOCATION_DESC:
                return ['location', 'DESC'];
            case ArticleOrder::ORDER_BY_LOCATION_ASC:
                return ['location', 'ASC'];
            case ArticleOrder::ORDER_BY_DATE_DESC:
                return ['expiration_date', 'DESC'];
            case ArticleOrder::ORDER_BY_DATE_ASC:
                return ['expiration_date', 'ASC'];
            default:
                return ['expiration_date', 'ASC'];
        }
    }
}
