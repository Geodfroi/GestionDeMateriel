<?php

declare(strict_types=1);

################################
## Joël Piguet - 2021.11.17 ###
##############################

namespace helpers;

use \PDO;
use helpers\DateFormatter;
use models\UserModel;
use models\ArticleModel;
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
     * Retrieve the UserModel ArticleModels;
     * 
     * @param int $UserModel_id UserModel id;
     * @param int $limit The maximum number of items to be returned.
     * @param int $skip The number of result items to be skipped before including them to the result array.
     * @return array An array of ArticleModels.
     */
    public function getUserArticles(int $UserModel_id, int $limit, int $offset = 0): array
    {
        $preparedStatement = $this->pdo->prepare('SELECT 
            id, 
            User_id, 
            Article_name, 
            location,
            comments, 
            expiration_date,
            creation_date 
        FROM Articles WHERE User_id = :uid LIMIT :lim OFFSET :off');

        $preparedStatement->bindParam(':uid', $UserModel_id, PDO::PARAM_INT);
        $preparedStatement->bindParam(':lim', $limit, PDO::PARAM_INT);
        $preparedStatement->bindParam(':off', $offset, PDO::PARAM_INT);

        $ArticleModels = [];
        try {
            if ($preparedStatement->execute()) {
                // fetch next as associative array until there are none to be fetched.
                while ($data = $preparedStatement->fetch()) {
                    array_push($ArticleModels, ArticleModel::fromDatabaseRow($data));
                }
            }
        } catch (\PDOException $e) {
            echo 'failure to retrieve ArticleModels from database: ' . $e->getMessage() . PHP_EOL;
        }
        return $ArticleModels;
    }

    /**
     * Return a single UserModel data by email.
     * 
     * @param string $email UserModel email.
     * @return ?UserModel UserModel class instance.
     */
    public function getUserByEmail(string $email): ?UserModel
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
        try {
            if ($preparedStatement->execute()) {
                $data = $preparedStatement->fetch(PDO::FETCH_ASSOC);
                return $data ? UserModel::fromDatabaseRow($data) : null;
            }
        } catch (\PDOException $e) {
            echo 'failure to retrieve UserModel from database: ' . $e->getMessage() . PHP_EOL;
        }

        return null;
    }

    /**
     * Return a single UserModel data by id.
     * 
     * @param int $id UserModel id.
     * @return ?UserModel UserModel class instance.
     */
    public function getUserById(int $id): ?UserModel
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

        try {
            if ($preparedStatement->execute()) {
                $data = $preparedStatement->fetch(PDO::FETCH_ASSOC);
                return $data ? UserModel::fromDatabaseRow($data) : null;
            }
        } catch (\PDOException $e) {
            echo 'failure to retrieve UserModel from database: ' . $e->getMessage() . PHP_EOL;
        }
        return null;
    }

    /**
     * Insert an ArticleModel to the database.
     * 
     * @param ArticleModel ArticleModel to insert.
     * @return bool True if the insert is successful.
     */
    public function insertArticle(ArticleModel $ArticleModel): bool
    {
        $preparedStatement = $this->pdo->prepare(
            'INSERT INTO Articles 
            (User_id, Article_name, location, expiration_date) 
            VALUES 
            (:uid, :art, :loc, :exp)'
        );

        $uid = $ArticleModel->getUser_id();
        $name = $ArticleModel->getArticleName();
        $location = $ArticleModel->getLocation();
        $date = DateFormatter::printSQLTimestamp($ArticleModel->getExpirationDate());
        $preparedStatement->bindParam(':uid', $uid, PDO::PARAM_INT);
        $preparedStatement->bindParam(':art', $name, PDO::PARAM_STR);
        $preparedStatement->bindParam(':loc', $location, PDO::PARAM_STR);
        $preparedStatement->bindParam(':exp', $date, PDO::PARAM_STR);

        try {
            return $preparedStatement->execute();
        } catch (\PDOException $e) {
            echo 'failure to insert ArticleModel to database: ' . $e->getMessage() . PHP_EOL;
        }
        return false;
    }



    /**
     * Update UserModel last_login field to now.
     * 
     * @param int $UserModelId ID of UserModel.
     */
    public function updateLogTime(int $UserModelId): bool
    {
        $preparedStatement = $this->pdo->prepare('UPDATE Users SET last_login=:last_login WHERE id = :id');
        $now = (new \DateTime("now", new \DateTimeZone("UTC")))->format('Y.m.d H:i:s');

        $preparedStatement->bindParam(':last_login', $now, PDO::PARAM_STR);
        $preparedStatement->bindParam(':id', $UserModelId, PDO::PARAM_INT);

        try {
            if ($preparedStatement->execute()) {
                return true;
            }
        } catch (\PDOException $e) {
            echo 'failure to update UserModel last_login: ' . $e->getMessage() . PHP_EOL;
        }
        return false;
    }
}
