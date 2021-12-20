<?php

declare(strict_types=1);

################################
## Joël Piguet - 2021.12.20 ###
##############################

namespace app\helpers;

use SQLite3;

use app\constants\AppPaths;

class TestUtil
{
    /**
     * Create fresh sqlite database at specified path. Replace existing.
     * 
     * @param string $local_path Path to db.
     * @return SQLite3 Opened db connection.
     */
    public static function createSQLiteDB(string $local_path)
    {
        //remove existing former db.
        unlink($local_path);

        $conn = new SQLite3($local_path);

        // create app tables.
        $q = file_get_contents(AppPaths::SQLITE_TABLES);
        $conn->exec($q);

        return $conn;
    }

    /**
     * Create and populate TestDB.
     */
    public static function populateSQLiteDB()
    {
        $conn =  TestUtil::CreateSQLiteDB(AppPaths::TEST_UNIT_DB);
        $q = file_get_contents(AppPaths::SQLITE_ENTRIES);

        $conn->exec($q);
        $conn->close();
    }
}
