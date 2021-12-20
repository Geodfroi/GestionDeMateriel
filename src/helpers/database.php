<?php

declare(strict_types=1);

################################
## Joël Piguet - 2021.12.20 ###
##############################

namespace app\helpers;

use \PDO;
use PDOException;
use SQLite3;

use app\constants\AppPaths;
use app\constants\LogError;
use app\constants\Globals;
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
    const SQLITE_CONN = 'Failure to establish sqlite3 connection';

    private LocationQueries $locations;
    private UserQueries $users;
    private ArticleQueries $articles;

    private array $data_types;
    private bool $use_sqlite;
    private $conn;

    /**
     * Initialise connection to the MySQL inside the constructor dunder method.
     * !! do not put the connection parameters (including admin password to db) in a settings.php file shared on github.
     */
    function __construct()
    {
        try {
            $this->use_sqlite = $GLOBALS[Globals::DATABASE] !== Globals::USE_MYSQL;
            $this->conn = $this->use_sqlite ? Database::getSQLiteConn() : Database::getMySQLConn();

            $this->data_types = [
                'int' => $this->use_sqlite ? SQLITE3_INTEGER : PDO::PARAM_INT,
                'str' => $this->use_sqlite ? SQLITE3_TEXT : PDO::PARAM_STR
            ];

            // $this->locations = new LocationQueries($conn, $use_sqlite);
            // $this->users = new UserQueries($conn, $use_sqlite);
            $this->articles = new ArticleQueries($this);
        } catch (PDOException $e) {
            Logging::error(LogError::CONN_FAILURE, ['error' =>  $e->getMessage()]);
        }
    }

    /**
     * Backup whole database to file.
     */
    public static function backup()
    {
        Database::getInstance()->articles()->backup();
        Database::getInstance()->locations()->backup();
        Database::getInstance()->users()->backup();
    }

    /**
     * Create and populate dummy database. Replace existing db at specified path.
     */
    public static function createDummy()
    {
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

    public function getConn()
    {
        return $this->conn;
    }

    public function getDataTypes()
    {
        return $this->data_types;
    }

    public function useSQLite()
    {
        return $this->use_sqlite;
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
     * Get connection to MySQL database using PDO.
     */
    private static function getMySQLConn(): PDO
    {
        $dsn = 'mysql:host=' . P_Settings::MYSQL_HOST . ';port=' . P_Settings::MYSQL_PORT . ';dbname=' . P_Settings::MYSQL_SCHEMA . ';charset=utf8mb4';
        $options = [
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ];

        return new PDO($dsn, P_Settings::MYSQL_ADMIN_ID, P_Settings::MYSQL_ADMIN_PASSWORD, $options);
    }

    /**
     * Get connection to local database using SQLite3.
     * https://www.tutorialspoint.com/sqlite/sqlite_php.htm
     */
    private static function getSQLiteConn(): SQLite3
    {
        $local_path = $GLOBALS[Globals::DATABASE] === Globals::USE_DEBUG_LOCAL ? AppPaths::DEBUG_LOCAL_DB : AppPaths::TEST_UNIT_DB;
        $conn = new SQLite3($local_path);

        Logging::info('Connection to SQLITE DB', ['path' => $local_path]);
        return $conn;
    }
}
