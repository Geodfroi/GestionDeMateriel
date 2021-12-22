<?php

declare(strict_types=1);

################################
## Joël Piguet - 2021.12.22 ###
##############################

namespace app\helpers;

use Exception;
use \PDO;
use PDOException;
use SQLite3;

use app\constants\AppPaths;
use app\constants\LogError;
use app\constants\PrivateSettings;
use app\helpers\App;
use app\helpers\Logging;
use app\helpers\db\ArticleQueries;
use app\helpers\db\LocationQueries;
use app\helpers\db\UserQueries;

/**
 * Database class accessible throughout the application.
 */
class Database
{
    const SQLITE_CONN = 'Failure to establish sqlite3 connection';

    private LocationQueries $locations;
    private UserQueries $users;
    private ArticleQueries $articles;

    /**
     * Initialise connection to the MySQL inside the constructor dunder method.
     * !! do not put the connection parameters (including admin password to db) in a settings.php file shared on github.
     * 
     * @param PDO|SQlite3 $conn Db connection.
     */
    function __construct($conn)
    {
        $this->locations = new LocationQueries($conn);
        $this->users = new UserQueries($conn);
        $this->articles = new ArticleQueries($conn);
    }

    /**
     * Backup whole database to file.
     */
    public static function backup()
    {
        throw new Exception('backup not implemented');
        // Database::getInstance()->articles()->backup();
        // Database::getInstance()->locations()->backup();
        // Database::getInstance()->users()->backup();
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
            try {
                $local_path = AppPaths::TEST_DB_FOLDER . DIRECTORY_SEPARATOR  . 'localDB.db';
                $conn = APP::useSQLite() ? Database::getSQLiteConn($local_path) : Database::getMySQLConn();
            } catch (PDOException $e) {
                Logging::error(LogError::CONN_FAILURE, ['error' =>  $e->getMessage()]);
            }
            $instance = new static($conn);
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

    /**
     * Get connection to MySQL database using PDO. Only one connection can ever exist.
     */
    public static function getMySQLConn(): PDO
    {
        static $instance;
        if (is_null($instance)) {
            $dsn = 'mysql:host=' . PrivateSettings::MYSQL_HOST . ';port=' . PrivateSettings::MYSQL_PORT . ';dbname=' . PrivateSettings::MYSQL_SCHEMA . ';charset=utf8mb4';
            $options = [
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ];
            $instance = new PDO($dsn, PrivateSettings::MYSQL_ADMIN_ID, PrivateSettings::MYSQL_ADMIN_PASSWORD, $options);

            if (App::isDebugMode()) {
                Logging::info('Connection to mysql', ['schema' => PrivateSettings::MYSQL_SCHEMA]);
            }
        }
        return $instance;
    }

    /**
     * Get connection to local database using SQLite3.
     * https://www.tutorialspoint.com/sqlite/sqlite_php.htm
     * 
     * @param string $local_path Path to local sqlite db. Several connections can exist to different local db.
     * @return SQLite3 SQLite3 conn.
     */
    public static function getSQLiteConn(string $local_path): SQLite3
    {
        $conn = new SQLite3($local_path);

        if (App::isDebugMode()) {
            Logging::info('Connection to SQLITE DB', ['path' => $local_path]);
        }
        return $conn;
    }
}
