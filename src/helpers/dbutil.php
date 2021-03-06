<?php

declare(strict_types=1);

################################
## Joël Piguet - 2022.04.28 ###
##############################

namespace app\helpers;

use app\constants\AppPaths;
use app\helpers\db\ArticleQueries;
use app\helpers\db\LocationQueries;
use app\helpers\db\UserQueries;
use app\helpers\Logging;

use DateTime;
use \PDO;
use SQLite3;

class DBUtil
{
    /**
     * Get connection to MySQL database using PDO. Only one connection can ever exist.
     */
    public static function getMySQLConn(): PDO
    {
        $dsn = 'mysql:host=' . MYSQL_HOST . ';port=' . MYSQL_PORT . ';dbname=' . MYSQL_SCHEMA . ';charset=utf8mb4';
        $options = [
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ];
        $instance = new PDO($dsn, MYSQL_ADMIN_ID, MYSQL_ADMIN_PASSWORD, $options);

        if (DEBUG_MODE) {
            Logging::info('Connection to mysql', ['schema' => MYSQL_SCHEMA]);
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

        if (DEBUG_MODE) {
            Logging::info('Connection to SQLITE DB', ['path' => $local_path]);
        }
        return $conn;
    }

    /**
     * Set up temporary sqlite db for tests and populate it with dummy data.
     * 
     * @param string $file_path DB file path
     * @param bool populate Populate with dummy data.
     * @return SQLite3|false SQLite3 db connection or false in case of error.
     */
    public static function localDBSetup(string $file_path, bool $populate = false): SQLite3
    {
        $path_info = pathinfo($file_path);
        $folder_path = $path_info['dirname'];

        // create folder if it doesn't exists
        if (!is_dir($folder_path)) {
            mkdir($folder_path, 0777, true);
        }

        if (file_exists($file_path)) {
            unlink($file_path);
        }

        $conn = DBUtil::getSQLiteConn($file_path);

        // create classes
        $content = file_get_contents(AppPaths::SQLITE_TABLES);
        if (!$conn->exec($content)) {
            return null;
        }

        // populate with dummy content;
        if ($populate) {
            $content = file_get_contents(AppPaths::SQLITE_ENTRIES);
            if (!$conn->exec($content)) {
                return null;
            }
        }
        return $conn;
    }

    /**
     * Backup the database to a local file inside backup_folder and delete older backups when the number of backups in folder exceeds the $max_files parameter.
     * 
     * @param PDO|SQlite3 $conn Db connection.
     * @param String $backup_folder Backup folder path.
     * @param int $max_files Maximum # of backup files in folder. Older files are deleted first.
     * @return bool True is successful.
     */
    public static function backup_db($conn, $backup_folder, $max_files = BACKUP_FILES_MAX): bool
    {
        $current_date = (new DateTime('now'))->format('Ymd');
        $path = $backup_folder . DIRECTORY_SEPARATOR . 'backup_' . $current_date . '.db';

        // establish backup connection and link it to sqlite backup.
        $backup_conn = DBUtil::localDBSetup($path);

        $backup_loc = (new LocationQueries($conn))->backup($backup_conn);
        $backup_users = (new UserQueries($conn))->backup($backup_conn);
        $backup_articles = (new ArticleQueries($conn))->backup($backup_conn);

        Util::eraseOldFiles($backup_folder, 'backup', 'db', $max_files);

        return $backup_loc &&  $backup_users && $backup_articles;
    }
}
