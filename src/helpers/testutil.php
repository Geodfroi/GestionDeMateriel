<?php

declare(strict_types=1);

################################
## Joël Piguet - 2022.01.08 ###
##############################

namespace app\helpers;

use SQLite3;
// use app\helpers\Logging;
use app\constants\AppPaths;

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
    public static function localDBSetup(string $folder_path, string $file_name, bool $populate): SQLite3
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
}
