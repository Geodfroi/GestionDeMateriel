<?php

declare(strict_types=1);

################################
## Joël Piguet - 2021.12.07 ###
##############################

namespace app\helpers;

use \PDO;
use PDOException;

use app\constants\P_Settings;
use app\helpers\db\ArticleQueries;
use app\helpers\db\LocationQueries;
use app\helpers\db\UserQueries;

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
            $dsn = 'mysql:host=' . P_Settings::HOST . ';port=' . P_Settings::PORT . ';dbname=' . P_Settings::DB_NAME . ';charset=utf8mb4';
            $options = [
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ];
            $pdo = new PDO($dsn, P_Settings::DB_ADMIN_ID, P_Settings::DB_ADMIN_PASSWORD, $options);
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
