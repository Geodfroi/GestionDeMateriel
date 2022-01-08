<?php

declare(strict_types=1);

################################
## Joël Piguet - 2022.01.08 ###
##############################

use app\constants\AppPaths;
use app\constants\Mode;
use app\helpers\App;
use app\helpers\Database;
// use app\helpers\Logging;
use app\helpers\TestUtil;

use PHPUnit\Framework\TestCase;
use function PHPUnit\Framework\assertTrue;

final class BackupTest extends TestCase
{
    private static $conn;

    public static function setUpBeforeClass(): void
    {
        App::setMode(Mode::TESTS_SUITE);

        if (APP::useSQLite()) {
            BackupTest::$conn = TestUtil::localDBSetup(AppPaths::TEST_DB_FOLDER, 'local', true);
        } else {
            BackupTest::$conn = Database::getMySQLConn();
        }
    }


    public static function tearDownAfterClass(): void
    {
    }

    public static function testBackup()
    {
        $backup_folder = AppPaths::TEST_DB_FOLDER . DIRECTORY_SEPARATOR . 'backups';

        // create dummy backups to test delete older backups functionality
        fopen($backup_folder . DIRECTORY_SEPARATOR . 'backup_20211009.db', 'w');
        fopen($backup_folder . DIRECTORY_SEPARATOR . 'backup_20211112.db', 'w');
        fopen($backup_folder . DIRECTORY_SEPARATOR . 'backup_20211223.db', 'w');
        fopen($backup_folder . DIRECTORY_SEPARATOR . 'backup_20212017.db', 'w');
        fopen($backup_folder . DIRECTORY_SEPARATOR . 'backup_20212018.db', 'w');

        assertTrue(TestUtil::backup_db(BackupTest::$conn,  $backup_folder, 4));
    }
}
