<?php

declare(strict_types=1);

################################
## Joël Piguet - 2021.12.22 ###
##############################

namespace app\helpers;

use SQLite3;

use app\constants\AppPaths;

class TestUtil
{
    /**
     * Set up temporary sqlite db for tests.
     * 
     * @param string $local_path Path to local db.
     * @param bool $populate Populate database with dummy entries.
     * @return SQLite3|false SQLite3 db connection or false in case of error.
     */
    public static function localDBSetup(string $local_path, bool $populate): SQLite3
    {
        // $local_path = AppPaths::TEST_DB_FOLDER . DIRECTORY_SEPARATOR . $db_name . '.db';
        if (file_exists($local_path)) {
            unlink($local_path); // erase existing
        }

        $conn = Database::getSQLiteConn($local_path);

        // create classes
        $content = file_get_contents(AppPaths::SQLITE_TABLES);
        if (!$conn->exec($content)) {
            return null;
        }

        if (!$populate) {
            return $conn;
        }

        // populate with dummy content;
        $content = file_get_contents(AppPaths::SQLITE_ENTRIES);
        if ($conn->exec($content)) {
            return $conn;
        }

        return null;
    }
}

    // /**
    //  * Create classes sqlite database at specified path. 
    //  * 
    //  * @param SQlite3 $conn Local db connection.
    //  * @return bool True if successful.
    //  */
    // private static function createClasses(SQLite3 $conn): bool
    // {
    // }

    // /**
    //  * Create and populate TestDB.
    //  * 
    //  * @param SQlite3 $conn Local db connection.
    //  * @return bool True if successful.
    //  */
    // public static function populate(SQLite3 $conn): bool
    // {
    // }
