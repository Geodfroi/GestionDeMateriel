<?php

declare(strict_types=1);

################################
## Joël Piguet - 2022.01.09 ###
##############################

use app\constants\AppPaths;
use app\constants\Mode;
use app\helpers\App;
use app\helpers\Logging;
use app\helpers\DBUtil;
use app\helpers\TestClass;
use PHPUnit\Framework\TestCase;
use function PHPUnit\Framework\assertTrue;

final class BackupTest extends TestClass
{
    public static function testBackup()
    {
        $conn = BackupTest::getConn();

        $backup_folder = AppPaths::TEST_DB_FOLDER . DIRECTORY_SEPARATOR . 'backups';
        if (!is_dir($backup_folder)) {
            mkdir($backup_folder, 0777, true);
        }

        // create dummy backups to test delete older backups functionality
        fopen($backup_folder . DIRECTORY_SEPARATOR . 'backup_20211009.db', 'w');
        fopen($backup_folder . DIRECTORY_SEPARATOR . 'backup_20211112.db', 'w');
        fopen($backup_folder . DIRECTORY_SEPARATOR . 'backup_20211223.db', 'w');
        fopen($backup_folder . DIRECTORY_SEPARATOR . 'backup_20212017.db', 'w');
        fopen($backup_folder . DIRECTORY_SEPARATOR . 'backup_20212018.db', 'w');

        assertTrue(DBUtil::backup_db($conn, $backup_folder, 4));
    }
}
