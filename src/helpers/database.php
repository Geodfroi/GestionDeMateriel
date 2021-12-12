<?php

declare(strict_types=1);

################################
## Joël Piguet - 2021.12.12 ###
##############################

namespace app\helpers;

use \PDO;
use PDOException;
use Monolog\Logger;

use app\constants\P_Settings;
use app\helpers\Logging;
use app\helpers\db\ArticleQueries;
use app\helpers\db\LocationQueries;
use app\helpers\db\UserQueries;


/**
 * Database class accessible throughout the application.
 */
class Database
{
    private LocationQueries $locations;
    private UserQueries $users;
    private ArticleQueries $articles;

    /**
     * Initialise connection to the MySQL inside the constructor dunder method.
     * !! do not put the connection parameters (including admin password to db) in a settings.php file shared on github.
     * 
     * @param Logger $logger Logger channel.
     */
    function __construct(Logger $Logger)
    {
        try {
            $dsn = 'mysql:host=' . P_Settings::HOST . ';port=' . P_Settings::PORT . ';dbname=' . P_Settings::DB_NAME . ';charset=utf8mb4';
            $options = [
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ];
            $pdo = new PDO($dsn, P_Settings::DB_ADMIN_ID, P_Settings::DB_ADMIN_PASSWORD, $options);
            $this->locations = new LocationQueries($pdo, $Logger);
            $this->users = new UserQueries($pdo, $Logger);
            $this->articles = new ArticleQueries($pdo, $Logger);
        } catch (PDOException $e) {
            error_log('Connection failed: ' . $e->getMessage() . PHP_EOL);
        }
    }

    /**
     * Get object for article queries.
     * 
     * @param Logger $logger Logger channel.
     * @return ArticleQueries article queries object.
     */
    public static function articles(Logger $logger = Logging::app()): ArticleQueries
    {
        return Database::getInstance($logger)->articles;
    }

    /**
     * Simpleton pattern insures there is only one instance of Database class in the whole application
     
     * @return Database Always return the same instance of Database class.
     */
    private static function getInstance(Logger $logger)
    {
        static $instance;
        if (is_null($instance)) {
            $instance = new static($logger);
        }
        return $instance;
    }

    /**
     * Get object for location queries.
     * 
     * @param Logger $logger Logger channel.
     * @return LocationQueries location queries object.
     */
    public static function locations(Logger $logger = Logging::app()): LocationQueries
    {
        return Database::getInstance($logger)->locations;
    }

    /**
     * Get object for user queries.
     * 
     * @param Logger $logger Logger channel.
     * @return UserQueries user queries object.
     */
    public static function users(Logger $logger = Logging::app()): UserQueries
    {
        return Database::getInstance($logger)->users;
    }
}
