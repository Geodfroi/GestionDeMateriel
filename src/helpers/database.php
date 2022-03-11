<?php

declare(strict_types=1);

################################
## Joël Piguet - 2022.03.11 ###
##############################

namespace app\helpers;

use \PDO;
use PDOException;
use SQLite3;

use app\constants\AppPaths;
use app\constants\LogError;
use app\helpers\DBUtil;
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

    private $conn;
    private $locations;
    private $users;
    private $articles;

    /**
     * Initialise connection to the MySQL inside the constructor dunder method.
     * !! do not put the connection parameters (including admin password to db) in a settings.php file shared on github.
     * 
     * @param PDO|SQlite3 $conn Db connection.
     */
    function __construct($conn)
    {
        $this->conn = $conn;
        $this->locations = new LocationQueries($conn);
        $this->users = new UserQueries($conn);
        $this->articles = new ArticleQueries($conn);
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

    public static function backup()
    {
        return DBUtil::backup_db(Database::getInstance()->conn, AppPaths::BACKUP_FOLDER);
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
                if (USE_SQLITE) {
                    $local_path = AppPaths::LOCAL_DB_FOLDER . DIRECTORY_SEPARATOR  . 'localDB.db';
                    $instance = new static(DBUtil::getSQLiteConn($local_path));
                } else {
                    $instance = new static(DBUtil::getMySQLConn());
                }
            } catch (PDOException $e) {
                Logging::error(LogError::CONN_FAILURE, ['error' =>  $e->getMessage()]);
            }
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
