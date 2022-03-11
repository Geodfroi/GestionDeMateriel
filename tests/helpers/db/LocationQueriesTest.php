<?php

declare(strict_types=1);

################################
## Joël Piguet - 2022.03.11 ###
##############################


use app\helpers\db\LocationQueries;
// use app\helpers\Logging;
use app\tests\TestClass;

use function PHPUnit\Framework\assertNotNull;
use function PHPUnit\Framework\assertNotSame;
use function PHPUnit\Framework\assertSame;
use function PHPUnit\Framework\assertTrue;

final class LocationQueriesTest extends TestClass
{
    private static LocationQueries $queries;

    public static function setUpBeforeClass(): void
    {
        LocationQueriesTest::$queries = new LocationQueries(LocationQueriesTest::getConn());
    }

    public static function tearDownAfterClass(): void
    {
    }

    // public static function testBackup()
    // {
    //     $backup_conn = DBUtil::localDBSetup(AppPaths::TEST_DB_FOLDER, 'locations_backup', false);
    //     assertNotNull($backup_conn);
    //     assertTrue(LocationQueriesTest::$queries->backup($backup_conn));
    // }

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
