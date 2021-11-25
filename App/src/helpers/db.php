<?php

declare(strict_types=1);

################################
## Joël Piguet - 2021.11.25 ###
##############################

namespace helpers;

use \PDO;
use PDOException;

use models\User;
use models\Article;

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
            $this->pdo = new PDO($dsn, DB_ADMIN_ID, DB_ADMIN_PASSWORD, $options);
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
     * @param int $id Article id.
     * @return bool True if the delete is successful.
     */
    public function deleteArticleById(int $id): bool
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
     * Delete all articles belonging to user.
     * 
     * @param int $user_id The user id.
     * @return bool True if the delete is successful.
     */
    public function deleteUserArticles($user_id): bool
    {
        $preparedStatement = $this->pdo->prepare('DELETE FROM articles WHERE user_id = :uid');
        $preparedStatement->bindParam(':uid', $user_id, PDO::PARAM_INT);
        if ($preparedStatement->execute()) {
            return true;
        }
        list(,, $error) = $preparedStatement->errorInfo();
        error_log('failure to delete user articles from database: ' . $error . PHP_EOL);
        return false;
    }

    /**
     * Delete the user from db by id.
     * 
     * @param int $id User id.
     * @return bool True if the delete is successful.
     */
    public function deleteUserByID($id): bool
    {
        $preparedStatement = $this->pdo->prepare('DELETE FROM users WHERE id = :id');
        $preparedStatement->bindParam(':id', $id, PDO::PARAM_INT);

        if ($preparedStatement->execute()) {
            return true;
        }
        list(,, $error) = $preparedStatement->errorInfo();
        error_log('failure to delete user from database: ' . $error . PHP_EOL);
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
        FROM articles WHERE id = :id');

        $preparedStatement->bindParam(':id', $id, PDO::PARAM_INT);

        if ($preparedStatement->execute()) {

            $data = $preparedStatement->fetch(); // retrieve only first row found; fine since id is unique.
            if ($data) {
                return Article::fromDatabaseRow($data);
            } else {
                error_log('failure to retrieve article from database: no row with index [' . $id . '] was found.');
            }
        } else {
            list(,, $error) = $preparedStatement->errorInfo();
            error_log('failure to retrieve article from database: ' . $error . PHP_EOL);
        }

        return null;
    }

    /**
     * Get number of articles owned by user.
     * 
     * @param int User id.
     * @return int # of articles or -1 if query fails.
     */
    public function getUserArticlesCount(int $user_id): int
    {
        $preparedStatement = $this->pdo->prepare('SELECT 
            COUNT(*)
            FROM articles
            WHERE user_id = :uid
        ');

        $preparedStatement->bindParam(':uid', $user_id, PDO::PARAM_INT);

        if ($preparedStatement->execute()) {
            $r = $preparedStatement->fetchColumn();
            return intval($r);
        }

        list(,, $error) = $preparedStatement->errorInfo();
        error_log('failure to count articles from database: ' . $error . PHP_EOL);
        return -1;
    }

    /**
     * Retrieve the User articles;
     * 
     * @param int $user_id User id;
     * @param int $limit The maximum number of items to be returned.
     * @param int $offset The number of result items to be skipped before including them to the result array.
     * @param int $orderby Order parameter. Use ArtOrder constants as parameter.
     * @return array An array of articles.
     */
    public function getUserArticles(int $user_id, int $limit, int $offset = 0, int $orderby = ArtOrder::DATE_DESC): array
    {
        [$order_column, $order_dir] = ArtOrder::getOrderParameters($orderby);

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

        $articles = [];

        if ($preparedStatement->execute()) {
            // fetch next as associative array until there are none to be fetched.
            while ($data = $preparedStatement->fetch()) {
                array_push($articles, Article::fromDatabaseRow($data));
            }
        } else {
            list(,, $error) = $preparedStatement->errorInfo();
            error_log('failure to retrieve article list from database: ' . $error . PHP_EOL);
        }

        return $articles;
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
     * Retrive user array from database.
     * 
     * @param bool $excludeAdmins Count only common users.
     * @param int $limit The maximum number of users to be returned.
     * @param int $offset The number of result users to be skipped before including them to the result array.
     * @param int $orderby Order parameter. Use UserOrder constants as parameter.
     * @return array Array of users.
     */
    public function getUsers(int $limit, int $offset = 0, int $orderby = UserOrder::EMAIL_ASC, bool $excludeAdmins = true): array
    {
        error_log('excludeAdmins : ' . $excludeAdmins);

        [$order_column, $order_dir] = UserOrder::getOrderParameters($orderby);

        // //Only data can be bound inside $preparedStatement, order-by params and where clause must be set before in the query string.
        $query_str = 'SELECT 
             id, 
             email, 
             password, 
             creation_date,
             last_login, 
             is_admin
         FROM users';
        if ($excludeAdmins) {
            $query_str .= " WHERE is_admin = False";
        }
        $query_str .= sprintf(' ORDER BY %s %s LIMIT :lim OFFSET :off', $order_column, $order_dir);

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
            error_log('failure to retrieve user list from database: ' . $error . PHP_EOL);
        }

        return $users;
    }

    /**
     * Count users in database user table.
     * 
     * @param bool $excludeAdmins Count only common users.
     */
    public function getUsersCount(bool $excludeAdmins = true)
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
        error_log('failure to count users from database: ' . $error . PHP_EOL);
        return -1;
    }

    /**
     * Insert an article into the database.
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
     * Insert a User into the database.
     * 
     * @param User $user User to insert.
     * @return bool True if the insert is successful.
     */
    public function insertUser(User $user): bool
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
        error_log('failure to insert user: ' . $error . PHP_EOL);
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
class ArtOrder
{
    const DATE_ASC = 0;
    const DATE_DESC = 1;
    const LOCATION_ASC = 2;
    const LOCATION_DESC = 3;
    const NAME_ASC = 4;
    const NAME_DESC = 5;

    /**
     * Return orderby query element.
     * 
     * @param int $const ArtOrder const value.
     * @return Array orderby string parameters.
     */
    public static function getOrderParameters(int $const): array
    {
        switch ($const) {
            case ArtOrder::DATE_ASC:
                return ['expiration_date', 'ASC'];
            case ArtOrder::DATE_DESC:
                return ['expiration_date', 'DESC'];
            case ArtOrder::LOCATION_ASC:
                return ['location', 'ASC'];
            case ArtOrder::LOCATION_DESC:
                return ['location', 'DESC'];
            case ArtOrder::NAME_ASC:
                return ['article_name', 'ASC'];
            case ArtOrder::NAME_DESC:
                return ['article_name', 'DESC'];
            default:
                return ['expiration_date', 'ASC'];
        }
    }
}

/**
 * Order enum implemented as const of a class.
 */
class UserOrder
{
    const CREATED_ASC = 0;
    const CREATED_DESC = 1;
    const EMAIL_ASC = 2;
    const EMAIL_DESC = 3;
    const LOGIN_ASC = 4;
    const LOGIN_DESC = 5;

    /**
     * Return orderby query element.
     * 
     * @param int $const UserOrder const value.
     * @return Array orderby string parameters.
     */
    public static function getOrderParameters(int $const): array
    {
        switch ($const) {
            case UserOrder::CREATED_ASC:
                return ['creation_date', 'ASC'];
            case UserOrder::CREATED_DESC:
                return ['creation_date', 'DESC'];
            case UserOrder::EMAIL_ASC:
                return ['email', 'ASC'];
            case UserOrder::EMAIL_DESC:
                return ['email', 'DESC'];
            case UserOrder::LOGIN_ASC:
                return ['last_login', 'ASC'];
            case UserOrder::LOGIN_DESC:
                return ['last_login', 'DESC'];
            default:
                return ['email', 'ASC'];
        }
    }
}
