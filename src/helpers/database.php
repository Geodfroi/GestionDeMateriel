<?php

declare(strict_types=1);

################################
## Joël Piguet - 2021.12.15 ###
##############################

namespace app\helpers;

use \PDO;
use PDOException;
use SQLite3;

use app\constants\LogChannel;
use app\constants\LogError;
use app\constants\P_Settings;
use app\constants\Settings;
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
     * @param string $logger Logger channel. Use LogChannel const.
     */
    function __construct(string $logger)
    {
        try {
            $conn = Settings::USE_SQLITE ? Database::getSQLiteConn() : Database::getMySQLConn();

            $this->locations = new LocationQueries($conn, $logger);
            $this->users = new UserQueries($conn, $logger);
            $this->articles = new ArticleQueries($conn, $logger);
        } catch (PDOException $e) {
            Logging::error(LogError::CONN_FAILURE, ['error' =>  $e->getMessage()], $logger);
        }
    }

    /**
     * Backup whole database to file.
     * 
     * @param string $logger Logger channel. Use LogChannel const.
     */
    public static function backup(string $logger = LogChannel::SERVER)
    {
        Database::getInstance($logger)->articles()->backup();
        Database::getInstance($logger)->locations()->backup();
        Database::getInstance($logger)->users()->backup();
    }

    /**
     * Get object for article queries.
     * 
     * @param string $logger Logger channel. Use LogChannel const.
     * @return ArticleQueries article queries object.
     */
    public static function articles(string $logger = LogChannel::APP): ArticleQueries
    {
        return Database::getInstance($logger)->articles;
    }

    /**
     * Simpleton pattern insures there is only one instance of Database class in the whole application
     * 
     * @param string $logger Logger channel. Use LogChannel const.
     * @return Database Always return the same instance of Database class.
     */
    private static function getInstance(string $logger = LogChannel::APP)
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
     * @param string $logger Logger channel. Use LogChannel const.
     * @return LocationQueries location queries object.
     */
    public static function locations(string $logger = LogChannel::APP): LocationQueries
    {
        return Database::getInstance($logger)->locations;
    }

    /**
     * Get object for user queries.
     * 
     * @param string $logger Logger channel. Use LogChannel const.
     * @return UserQueries user queries object.
     */
    public static function users(string $logger = LogChannel::APP): UserQueries
    {
        return Database::getInstance($logger)->users;
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
        $conn = new SQLite3('local.db');

        // create app tables.
        $sql = <<<EOF
            CREATE TABLE IF NOT EXISTS articles (
            id                INTEGER         NOT NULL PRIMARY KEY AUTOINCREMENT,
            article_name      varchar(255)    NOT NULL,
            comments          varchar(255),
            creation_date     timestamp       NOT NULL DEFAULT CURRENT_TIMESTAMP,
            expiration_date   timestamp       NOT NULL,
            location          varchar(255)    NOT NULL,
            user_id           INTEGER         NOT NULL)
        EOF;
        $conn->exec($sql);

        $sql = <<<EOF
            CREATE TABLE IF NOT EXISTS users (
                id            INTEGER         NOT NULL PRIMARY KEY AUTOINCREMENT,
                alias         varchar(255)    UNIQUE,
                login_email   varchar(255)    NOT NULL UNIQUE,
                contact_email varchar(255),
                contact_delay varchar(255)    NOT NULL DEFAULT '3-14',
                password      varchar(255)    NOT NULL,
                creation_date timestamp       NOT NULL DEFAULT CURRENT_TIMESTAMP,
                last_login    timestamp       DEFAULT CURRENT_TIMESTAMP,
                is_admin      boolean         DEFAULT false
            )
        EOF;
        $conn->exec($sql);

        $sql = <<<EOF
            CREATE TABLE IF NOT EXISTS locations (
                id            INTEGER         NOT NULL PRIMARY KEY AUTOINCREMENT,
                str_content   varchar(255)    NOT NULL
            )
        EOF;
        $conn->exec($sql);

        return $conn;
    }
}
