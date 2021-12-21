<?php

declare(strict_types=1);

################################
## Joël Piguet - 2021.12.21 ###
##############################

use app\constants\AppPaths;
use app\constants\LogChannel;
use app\helpers\App;
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
    /**
     * Set up and access local test db.
     */
    private static function queries()
    {
        static $instance;
        if (is_null($instance)) {
            $local_path = AppPaths::TEST_DB_FOLDER . DIRECTORY_SEPARATOR  . 'testLocations.db';
            $db = TestUtil::localDBSetup($local_path);;
            $instance = new LocationQueries($db);
        }
        return $instance;
    }

    public static function setUpBeforeClass(): void
    {
        App::setConfig(LogChannel::TEST, true, true);
    }

    public static function tearDownAfterClass(): void
    {
    }

    public function testDelete(): void
    {
        assertTrue(LocationQueriesTest::queries()->delete(2));
    }

    /**
     * @dataProvider stringProvider
     */
    public function testInsert(?string $str, bool $isZero): void
    {
        $id =  LocationQueriesTest::queries()->insert($str);

        if ($isZero) {
            assertSame($id, 0);
        } else {
            assertNotSame($id, 0);
        }
    }

    public function stringProvider(): array
    {
        return [
            ['', true],
            [null, true],
            ['behind the sofa', false],
        ];
    }

    public function testQueryById(): StringContent
    {
        $loc = LocationQueriesTest::queries()->queryById(1);
        assertNotNull($loc);
        // Logging::debug($loc->__toString());
        return $loc;
    }


    public function testQueryAll(): void
    {
        $array = LocationQueriesTest::queries()->queryAll();
        // foreach ($array as $loc) {
        //     Logging::debug($loc->__toString());
        // }
        assertTrue(count($array) > 0);
    }


    public function testContentExists(): void
    {
        $content = LocationQueriesTest::queries()->queryById(1)->getContent();
        assertTrue(LocationQueriesTest::queries()->contentExists($content));
    }

    /**
     * @depends testQueryById
     */
    public function testUpdate(StringContent $content): void
    {
        assertTrue(LocationQueriesTest::queries()->update($content->getId(), "On top of the chimney"));
        $q = LocationQueriesTest::queries()->queryById($content->getId());
        assertSame($q->getContent(), "On top of the chimney");
    }
}
