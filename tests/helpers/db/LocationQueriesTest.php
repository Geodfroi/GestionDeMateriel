<?php

declare(strict_types=1);

################################
## Joël Piguet - 2022.01.08 ###
##############################

use app\constants\AppPaths;
use app\constants\Mode;
use app\helpers\App;
use app\helpers\Database;
use app\helpers\db\LocationQueries;
use app\helpers\Logging;
use app\helpers\TestUtil;
use app\models\StringContent;

use PHPUnit\Framework\TestCase;
use function PHPUnit\Framework\assertNotNull;
use function PHPUnit\Framework\assertNotSame;
use function PHPUnit\Framework\assertSame;
use function PHPUnit\Framework\assertTrue;

final class LocationQueriesTest extends TestCase
{
    private static LocationQueries $queries;

    public static function setUpBeforeClass(): void
    {
        App::setMode(Mode::TESTS_SUITE);

        if (APP::useSQLite()) {
            $conn = TestUtil::localDBSetup(AppPaths::TEST_DB_FOLDER, 'locations', true);
        } else {
            $conn = Database::getMySQLConn();
        }
        LocationQueriesTest::$queries = new LocationQueries($conn);
    }

    public static function tearDownAfterClass(): void
    {
    }

    public static function testBackup()
    {
        $folder = AppPaths::TEST_DB_FOLDER . DIRECTORY_SEPARATOR . 'backup';
        $backup_conn = TestUtil::localDBSetup($folder, 'locations', false);
        assertNotNull($backup_conn);
        assertTrue(LocationQueriesTest::$queries->backup($backup_conn));
    }


    public function insertProvider(): array
    {
        return [
            ['', true],
            [null, true],
            ['behind the sofa', false],
        ];
    }
    /**
     * @dataProvider insertProvider
     */
    public function testInsert(?string $str, bool $isZero): void
    {
        $id =  LocationQueriesTest::$queries->insert($str);

        if ($isZero) {
            assertSame($id, 0);
        } else {
            assertNotSame($id, 0);
        }
    }

    public function testInsertDelete(): void
    {
        $id =  LocationQueriesTest::$queries->insert("new entry str");
        assertTrue(LocationQueriesTest::$queries->delete($id));
    }


    public function testQueryById(): void
    {
        $id =  LocationQueriesTest::$queries->insert("another str");
        $loc = LocationQueriesTest::$queries->queryById($id);
        assertNotNull($loc);
    }


    public function testQueryAll(): void
    {
        $array = LocationQueriesTest::$queries->queryAll();
        // foreach ($array as $loc) {
        //     Logging::debug($loc->__toString());
        // }
        assertTrue(count($array) > 0);
    }


    public function testContentExists(): void
    {
        LocationQueriesTest::$queries->insert("yet another str");
        assertTrue(LocationQueriesTest::$queries->contentExists("yet another str"));
    }


    public function testUpdate(): void
    {
        $id =  LocationQueriesTest::$queries->insert("yet another another str");
        assertTrue(LocationQueriesTest::$queries->update($id, "On top of the chimney"));
        $q = LocationQueriesTest::$queries->queryById($id);
        assertSame($q->getContent(), "On top of the chimney");
    }
}
