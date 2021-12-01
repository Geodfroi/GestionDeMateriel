<?php

declare(strict_types=1);

################################
## Joël Piguet - 2021.12.01 ###
##############################

namespace helpers;

use \PDO;
use PDOException;

use models\User;
use models\Article;
use models\StringContent;

/**
 * Database class accessible throughout the application by calling it'ss get_instance() method. 
 */
class Database
{
    private LocationQueries $locations;
    private UserQueries $users;
    private ArticleQueries $articles;

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
            $pdo = new PDO($dsn, DB_ADMIN_ID, DB_ADMIN_PASSWORD, $options);
            $this->locations = new LocationQueries($pdo);
            $this->users = new UserQueries($pdo);
            $this->articles = new ArticleQueries($pdo);
        } catch (PDOException $e) {
            error_log('Connection failed: ' . $e->getMessage() . PHP_EOL);
        }
    }

    /**
     * Get object for article queries.
     * 
     * @return ArticleQueries article queries object.
     */
    public static function articles(): ArticleQueries
    {
        return Database::getInstance()->articles;
    }

    /**
     * Simpleton pattern insures there is only one instance of Database class in the whole application
     * 
     * @return Database Always return the same instance of Database class.
     */
    private static function getInstance()
    {
        static $instance;
        if (is_null($instance)) {
            $instance = new static();
        }
        return $instance;
    }

    /**
     * Get object for location queries.
     * 
     * @return LocationQueries location queries object.
     */
    public static function locations(): LocationQueries
    {
        return Database::getInstance()->locations;
    }

    /**
     * Get object for user queries.
     * 
     * @return UserQueries user queries object.
     */
    public static function users(): UserQueries
    {
        return Database::getInstance()->users;
    }
}

/**
 * Regroup function to interact with article table.
 */
class ArticleQueries
{
    private PDO $pdo;

    function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Delete the article from db by id.
     * 
     * @param int $id Article id.
     * @return bool True if the delete is successful.
     */
    public function delete(int $id): bool
    {
        $preparedStatement = $this->pdo->prepare('DELETE FROM articles WHERE id = :id');
        $preparedStatement->bindParam(':id', $id, PDO::PARAM_INT);

        if ($preparedStatement->execute()) {
            return true;
        }
        list(,, $error) = $preparedStatement->errorInfo();
        error_log(ARTICLE_DELETE . $error . PHP_EOL);
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
        error_log(USER_ARTICLES_DELETE . $error . PHP_EOL);
        return false;
    }

    /**
     * Insert an article into the database.
     * 
     * @param Article $article Article to insert.
     * @return bool True if the insert is successful.
     */
    public function insert(Article $article): bool
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
        error_log(ARTICLE_INSERT . $error . PHP_EOL);
        return false;
    }

    /**
     * Get number of articles owned by user.
     * 
     * @param int User id.
     * @return int # of articles or -1 if query fails.
     */
    public function queryCountByUser(int $user_id): int
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
        error_log(ARTICLES_COUNT_QUERY . $error . PHP_EOL);
        return -1;
    }

    /**
     * Retrieve a single Article by it's id.
     * 
     * @param int $id Article's id.
     * @return Article|null
     */
    public function queryById(int $id): ?Article
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
            error_log(ARTICLE_QUERY . $error . PHP_EOL);
        }

        return null;
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
    public function queryAllByUser(int $user_id, int $limit, int $offset = 0, int $orderby = ArtOrder::DATE_DESC): array
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
            error_log(ARTICLES_QUERY . $error . PHP_EOL);
        }

        return $articles;
    }

    /**
     * Update article in database.
     * 
     * @param Article Article to be updated.
     * @return bool True if update is successful.
     */
    public function update(Article $article): bool
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
}

/**
 * Regroup function to interact with locations table.
 */
class LocationQueries
{
    private PDO $pdo;

    function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Delete location from db by id.
     * 
     * @param int $id Location id.
     * @return bool True if the delete is successful.
     */
    public function delete(int $id): bool
    {
        $preparedStatement = $this->pdo->prepare('DELETE FROM locations WHERE id = :id');
        $preparedStatement->bindParam(':id', $id, PDO::PARAM_INT);

        if ($preparedStatement->execute()) {
            return true;
        }
        list(,, $error) = $preparedStatement->errorInfo();
        error_log(LOCATION_DELETE . $error . PHP_EOL);
        return false;
    }

    /**     
     * Insert a loaction string into the database.
     * 
     * @param string $str New location string.
     * @return bool True if the insert is successful.
     */
    public function insert(string $str): bool
    {
        $preparedStatement = $this->pdo->prepare('INSERT INTO locations (str_content) VALUES (:str)');
        $preparedStatement->bindParam(':str', $str, PDO::PARAM_STR);

        if ($preparedStatement->execute()) {
            return true;
        }
        list(,, $error) = $preparedStatement->errorInfo();
        error_log(LOCATION_INSERT . $error . PHP_EOL);
        return false;
    }

    /**
     * Retrieve user array from database.
     * 
     * @return array Array of locations.
     */
    public function queryAll(): array
    {
        $preparedStatement = $this->pdo->prepare('SELECT id, str_content FROM locations ORDER BY str_content ASC');

        $locations = [];

        if ($preparedStatement->execute()) {
            // fetch next as associative array until there are none to be fetched.
            while ($data = $preparedStatement->fetch()) {
                array_push($locations, StringContent::fromDatabaseRow($data));
            }
        } else {
            list(,, $error) = $preparedStatement->errorInfo();
            error_log(LOCATIONS_QUERY_ALL  . $error . PHP_EOL);
        }

        return $locations;
    }

    /**
     * Check if content already exists in database
     * @param string $content Location content.
     * @return bool True if already present.
     */
    public function contentExists(string $content): bool
    {
        $preparedStatement = $this->pdo->prepare('SELECT 
            COUNT(*)
            FROM locations
            WHERE str_content = :str');

        $preparedStatement->bindParam(':str', $content, PDO::PARAM_STR);
        if ($preparedStatement->execute()) {
            $r = $preparedStatement->fetchColumn();
            return intval($r) === 1;
        }
        list(,, $error) = $preparedStatement->errorInfo();
        error_log(LOCATIONS_CHECK_CONTENT . $error . PHP_EOL);
        return false;
    }

    /**
     * Update user alias.
     * 
     * @param int $location_id Location object id.
     * @param string $str New location string.
     * @return bool True is update is successful.
     */
    public function update(int $location_id, string $str)
    {
        $preparedStatement = $this->pdo->prepare('UPDATE locations SET str_content=:str WHERE id = :id');

        $preparedStatement->bindParam(':id', $location_id, PDO::PARAM_INT);
        $preparedStatement->bindParam(':str', $str, PDO::PARAM_STR);

        if ($preparedStatement->execute()) {
            return true;
        }
        list(,, $error) = $preparedStatement->errorInfo();
        error_log(LOCATION_UPDATE . $error . PHP_EOL);
        return false;
    }
}

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
        error_log(USER_DELETE . $error . PHP_EOL);
        return false;
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
        error_log(USER_INSERT . $error . PHP_EOL);
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
        error_log(USER_QUERY . $error . PHP_EOL);
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
        error_log(USER_QUERY . $error . PHP_EOL);
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
        error_log(USERS_COUNT_QUERY . $error . PHP_EOL);
        return -1;
    }

    /**
     * Retrieve user array from database.
     * 
     * @param bool $excludeAdmins Count only common users.
     * @param int $limit The maximum number of users to be returned.
     * @param int $offset The number of result users to be skipped before including them to the result array.
     * @param int $orderby Order parameter. Use UserOrder constants as parameter.
     * @return array Array of users.
     */
    public function queryAll(int $limit, int $offset = 0, int $orderby = UserOrder::EMAIL_ASC, bool $excludeAdmins = true): array
    {
        [$order_column, $order_dir] = UserOrder::getOrderParameters($orderby);

        // //Only data can be bound inside $preparedStatement, order-by params and where clause must be set before in the query string.
        $query_str = 'SELECT 
             id, 
             contact_email,
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
            error_log(USERS_QUERY  . $error . PHP_EOL);
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
        error_log(USER_ALIAS_UPDATE . $error . PHP_EOL);
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
        error_log(USER_DELAY_UPDATE . $error . PHP_EOL);
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
        error_log(USER_CONTACT_UPDATE . $error . PHP_EOL);
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
        error_log(USER_LOGTIME_UPDATE . $error . PHP_EOL);
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
        error_log(USER_PASSWORD_UPDATE  . $error . PHP_EOL);
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
