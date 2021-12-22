<?php

declare(strict_types=1);

################################
## Joël Piguet - 2021.12.21 ###
##############################

use app\constants\AppPaths;
use app\constants\Mode;
use app\constants\OrderBy;
use app\helpers\App;
use app\helpers\Database;
use app\helpers\Logging;
use app\helpers\TestUtil;
use app\helpers\db\UserQueries;
use app\models\User;

use PHPUnit\Framework\TestCase;

use function PHPUnit\Framework\assertFalse;
use function PHPUnit\Framework\assertNotNull;
use function PHPUnit\Framework\assertNotSame;
use function PHPUnit\Framework\assertNull;
use function PHPUnit\Framework\assertSame;
use function PHPUnit\Framework\assertTrue;

final class UserQueriesTest extends TestCase
{
    private static UserQueries $queries;

    public static function setUpBeforeClass(): void
    {
        App::setMode(Mode::TESTS_SUITE);

        if (APP::useSQLite()) {
            $local_path = AppPaths::TEST_DB_FOLDER . DIRECTORY_SEPARATOR  . 'testUsers.db';
            $conn = TestUtil::localDBSetup($local_path);;
        } else {
            $conn = Database::getMySQLConn();
        }

        UserQueriesTest::$queries = new UserQueries($conn);
    }

    public static function tearDownAfterClass(): void
    {
    }

    public function testDelete()
    {
        assertTrue(UserQueriesTest::$queries->delete(3));
    }


    public function testInsert()
    {
        $user = User::fromForm('my.email@gmail.com', '0123456789', true);

        $count = UserQueriesTest::$queries->queryCount(false);
        $id = UserQueriesTest::$queries->insert($user);

        assertNotSame($id, 0);
        assertSame($count + 1, UserQueriesTest::$queries->queryCount(false));
        // Logging::info('testInsert', ['queryCount' => strval($count)]);
    }

    /**
     * @dataProvider queryByAliasProvider
     */
    public function testQueryByAlias(string $alias, bool $is_present): void
    {
        $user = UserQueriesTest::$queries->queryByAlias($alias);
        if ($is_present) {
            assertNotNull($user);
        } else {
            assertNull($user);
        }
    }

    public function queryByAliasProvider(): array
    {
        return [
            ['noel.biquet@gmail.com', true], // present
            ['Henry', false], //not present
            ['Florence', true], //not present
        ];
    }

    /**
     * @dataProvider queryByEmailProvider
     */
    public function testQueryByEmail(string $email, bool $is_present): void
    {
        $user = UserQueriesTest::$queries->queryByEmail($email);
        if ($is_present) {
            assertNotNull($user);
        } else {
            assertNull($user);
        }
    }

    public function queryByEmailProvider(): array
    {
        return [
            ['noel.biquet@gmail.com', true], // present
            ['arrow@gmail.com', false], //not present
            ['', false], //not present
        ];
    }

    /**
     * @dataProvider queryByIdProvider
     */
    public function testQueryById(int $id, bool $is_present): void
    {
        $user = UserQueriesTest::$queries->queryById($id);
        if ($is_present) {
            assertNotNull($user);
        } else {
            assertNull($user);
        }
    }

    public function queryByIdProvider(): array
    {
        return [
            [24, false], //not present
            [1, true], // present
            [0, false], //not present
        ];
    }

    public function testQueryAll()
    {
        $array = UserQueriesTest::$queries->queryAll(PHP_INT_MAX, 0, OrderBy::EMAIL_DESC, false);
        // foreach ($array as $user) {
        //     Logging::debug($user->__tostring());
        // }
        // Logging::debug('testQueryAll', ['count' => strval(count($array))]);
        $this->assertNotTrue(count($array) === 0);
    }

    public function testQueryCount()
    {
        $count = UserQueriesTest::$queries->queryCount(false);
        $this->assertTrue($count > 0);
        $count2 = UserQueriesTest::$queries->queryCount(true);
        $this->assertTrue($count2 > 0);
        $this->assertNotEquals($count,  $count2);
    }

    /**
     * @dataProvider updateAliasProvider
     */
    public function testUpdateAlias(int $user_id, string $alias, bool $expect_success): void
    {
        $r = UserQueriesTest::$queries->updateAlias($user_id, $alias);
        if ($expect_success) {
            assertTrue($r);
        } else {
            assertFalse($r);
        }
    }

    public function updateAliasProvider(): array
    {
        return [
            [2, 'Bertrand', true],
            [22, 'Geneveve', true], //not found id.
            [3, '', false], //invalid alias
        ];
    }

    public function testUpdateContactDelay(): void
    {
        assertTrue(UserQueriesTest::$queries->updateContactDelay(2, '3-7-14'));
    }

    public function testUpdateContactEmail(): void
    {
        assertTrue(UserQueriesTest::$queries->updateContactEmail(2, 'contact.me@gmail.com'));
    }

    public function testUpdateLogTime(): void
    {
        assertTrue(UserQueriesTest::$queries->updateLogTime(3));
    }

    public function testupdatePassword(): void
    {
        assertTrue(UserQueriesTest::$queries->updateLogTime(3, '12345678'));
    }
}
