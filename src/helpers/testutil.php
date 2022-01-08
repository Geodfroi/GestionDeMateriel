<?php

declare(strict_types=1);

################################
## Joël Piguet - 2022.01.08 ###
##############################

namespace app\helpers;

use app\constants\AppPaths;
use app\helpers\db\ArticleQueries;
use app\helpers\db\LocationQueries;
use app\helpers\db\UserQueries;
use app\helpers\Logging;
use DateTime;
use SQLite3;
use DirectoryIterator;

class TestUtil
{
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
            //create new file under new name as bugs occurs when recreating a file which has just been unset.
            // $file_path = $folder_path . DIRECTORY_SEPARATOR . $file_name . '2.db';
        }

        $conn = Database::getSQLiteConn($file_path);

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
     * Backup the database to a local file inside backup_folder and delete older backups.
     * 
     * @param PDO|SQlite3 $conn Db connection.
     * @param String $backup_folder Backup folder path.
     * @param int $limit Maximum # of backup files in folder. Older files are deleted first.
     * @return bool True is successful.
     */
    public static function backup_db($conn, $backup_folder, $limit = 20): bool
    {
        $current_date = (new DateTime('now'))->format('Ymd');

        // establish backup connection.
        $backup_conn = TestUtil::localDBSetup($backup_folder, 'backup_' . $current_date);

        $backup_loc = (new LocationQueries($conn))->backup($backup_conn);
        $backup_users = (new UserQueries($conn))->backup($backup_conn);
        $backup_articles = (new ArticleQueries($conn))->backup($backup_conn);

        $tuples = [];

        // delete older backups in excess of limit.
        foreach (new DirectoryIterator($backup_folder) as $file) {
            if (!$file->isDot()) {
                $file_name = $file->getFilename();

                // check if file name match the pattern 'backup_20220101.db' with regex
                if (preg_match("/^backup_\d{8}\.db$/", $file_name)) {
                    Logging::debug('match:' . $file_name);
                    // recover date from file name;
                    $str_date = substr($file_name, strlen('backup_'), 8);
                    $date = DateTime::createFromFormat('Ymd', $str_date);
                    Logging::debug('match:' . $date->format('Y-m-d'));
                }
            }
        }


        return false;
        // return  $backup_loc &&  $backup_users && $backup_articles;

        // $folder = AppPaths::TEST_DB_FOLDER . DIRECTORY_SEPARATOR . 'backup';
        // $backup_conn = TestUtil::localDBSetup($folder, '', false);
        // assertNotNull($backup_conn);
        // assertTrue(LocationQueriesTest::$queries->backup($backup_conn));

        // return false;
    }
}
