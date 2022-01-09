<?php

declare(strict_types=1);

################################
## Joël Piguet - 2022.01.09 ###
##############################

namespace app\helpers;

use app\constants\AppPaths;
use app\constants\PrivateSettings;
use app\constants\Settings;
use app\helpers\db\ArticleQueries;
use app\helpers\db\LocationQueries;
use app\helpers\db\UserQueries;
use app\helpers\Logging;

use DateTime;
use DirectoryIterator;
use \PDO;
use SQLite3;

class DBUtil
{
    /**
     * Get connection to MySQL database using PDO. Only one connection can ever exist.
     */
    public static function getMySQLConn(): PDO
    {
        $dsn = 'mysql:host=' . PrivateSettings::MYSQL_HOST . ';port=' . PrivateSettings::MYSQL_PORT . ';dbname=' . PrivateSettings::MYSQL_SCHEMA . ';charset=utf8mb4';
        $options = [
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ];
        $instance = new PDO($dsn, PrivateSettings::MYSQL_ADMIN_ID, PrivateSettings::MYSQL_ADMIN_PASSWORD, $options);

        if (App::isDebugMode()) {
            Logging::info('Connection to mysql', ['schema' => PrivateSettings::MYSQL_SCHEMA]);
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

    /**
     * Set up temporary sqlite db for tests and populate it with dummy data.
     * 
     * @param string $folder_path
     * @param string $file_name File name without ext.
     * @param bool populate Populate with dummy data.
     * @return SQLite3|false SQLite3 db connection or false in case of error.
     */
    public static function localDBSetup(string $folder_path, string $file_name, bool $populate = false): SQLite3
    {
        // create folder if it doesn't exists
        if (!is_dir($folder_path)) {
            mkdir($folder_path, 0777, true);
        }

        $file_path = $folder_path . DIRECTORY_SEPARATOR . $file_name . '.db';
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
    public static function backup_db($conn, $backup_folder, $max_files = Settings::BACKUP_FILES_MAX): bool
    {
        $current_date = (new DateTime('now'))->format('Ymd');

        // establish backup connection and link it to sqlite backup.
        $backup_conn = DBUtil::localDBSetup($backup_folder, 'backup_' . $current_date);

        $backup_loc = (new LocationQueries($conn))->backup($backup_conn);
        $backup_users = (new UserQueries($conn))->backup($backup_conn);
        $backup_articles = (new ArticleQueries($conn))->backup($backup_conn);

        $dates = [];

        // find backup files in folder and extract their date component.
        foreach (new DirectoryIterator($backup_folder) as $file) {
            if (!$file->isDot()) {
                $file_name = $file->getFilename();
                // check if file name match the pattern 'backup_20220101.db' with regex
                if (preg_match("/^backup_\d{8}\.db$/", $file_name)) {

                    // recover date from file name;
                    $str_date = substr($file_name, strlen('backup_'), 8);
                    array_push($dates, $str_date);
                }
            }
        }

        // sort date array in reverse alphabetical order => oldest date will be last in array.
        rsort($dates);

        // cull oldest files when the number of file exceeds $max_files limit.
        for ($n = $max_files; $n < count($dates); $n++) {
            $date = $dates[$n];
            $file_path = $backup_folder . DIRECTORY_SEPARATOR . 'backup_' . $date . '.db';
            unlink($file_path);
        }

        return $backup_loc &&  $backup_users && $backup_articles;
        // return $backup_users;
        // return $backup_articles;
    }
}
