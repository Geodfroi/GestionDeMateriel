<?php

declare(strict_types=1);

################################
## Joël Piguet - 2021.12.21 ###
##############################

namespace app\helpers;

use SQLite3;

use app\constants\AppPaths;

class TestUtil
{
    /**
     * Create classes sqlite database at specified path. 
     * 
     * @param SQlite3 $conn Local db connection.
     * @return bool True if successful.
     */
    public static function createClasses(SQLite3 $conn): bool
    {
        $q = file_get_contents(AppPaths::SQLITE_TABLES);
        return $conn->exec($q);
    }

    /**
     * Create and populate TestDB.
     * 
     * @param SQlite3 $conn Local db connection.
     * @return bool True if successful.
     */
    public static function populate(SQLite3 $conn): bool
    {
        if (TestUtil::createClasses($conn)) {
            $q = file_get_contents(AppPaths::SQLITE_ENTRIES);
            return $conn->exec($q);
        }
        return false;
    }

    /**
     * Set up temporary sqlite db for tests.
     * 
     * @param string $local_path Path to local db.
     * @return Database Database instance or null in case of error.
     */
    public static function localDBSetup(string $local_path): Database
    {
        // $local_path = AppPaths::TEST_DB_FOLDER . DIRECTORY_SEPARATOR . $db_name . '.db';
        if (file_exists($local_path)) {
            unlink($local_path); // erase existing
        }

        $conn = Database::getSQLiteConn($local_path);
        if (TestUtil::populate($conn)) {
            return new Database($conn, true);
        }
        return null;
    }
}
