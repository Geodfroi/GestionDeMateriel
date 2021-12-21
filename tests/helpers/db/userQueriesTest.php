<?php

declare(strict_types=1);

################################
## Joël Piguet - 2021.12.21 ###
##############################

use app\constants\Globals;
use app\constants\LogChannel;
use app\helpers\db\UserQueries;
use app\helpers\TestUtil;
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
    /**
     * Set up and access local test db.
     */
    private static function queries()
    {
        static $instance;
        if (is_null($instance)) {
            $db = TestUtil::setupTestDB('testUsers');
            $instance = new UserQueries($db);
        }
        return $instance;
    }

    public static function setUpBeforeClass(): void
    {
        $GLOBALS[Globals::LOG_CHANNEL] = LogChannel::TEST;
    }

    public static function tearDownAfterClass(): void
    {
    }

    public function testDelete()
    {
        assertTrue(UserQueriesTest::queries()->delete(3));
    }


    public function testInsert()
    {
        $user = User::fromForm('my.email@gmail.com', '0123456789', true);

        $count = UserQueriesTest::queries()->queryCount(false);
        $id = UserQueriesTest::queries()->insert($user);

        assertNotSame($id, 0);
        assertSame($count + 1, UserQueriesTest::queries()->queryCount(false));
        // Logging::info('testInsert', ['queryCount' => strval($count)]);
    }

    /**
     * @dataProvider aliasProvider
     */
    public function testQueryByAlias(string $alias, bool $is_present): void
    {
        $user = UserQueriesTest::queries()->queryByAlias($alias);
        if ($is_present) {
            assertNotNull($user);
        } else {
            assertNull($user);
        }
    }

    public function aliasProvider(): array
    {
        return [
            ['noel.biquet@gmail.com', true], // present
            ['Henry', false], //not present
            ['Florence', true], //not present
        ];
    }

    /**
     * @dataProvider emailProvider
     */
    public function testQueryByEmail(string $email, bool $is_present): void
    {
        $user = UserQueriesTest::queries()->queryByEmail($email);
        if ($is_present) {
            assertNotNull($user);
        } else {
            assertNull($user);
        }
    }

    public function emailProvider(): array
    {
        return [
            ['noel.biquet@gmail.com', true], // present
            ['arrow@gmail.com', false], //not present
            ['', false], //not present
        ];
    }

    /**
     * @dataProvider idProvider
     */
    public function testQueryById(int $id, bool $is_present): void
    {
        $user = UserQueriesTest::queries()->queryById($id);
        if ($is_present) {
            assertNotNull($user);
        } else {
            assertNull($user);
        }
    }

    // public function testQueryCount(bool $excludeAdmins = true)

    public function idProvider(): array
    {
        return [
            [24, false], //not present
            [1, true], // present
            [0, false], //not present
        ];
    }

    public function testQueryAll()
    {
        throw new Exception();
    }

    public function testQueryCount()
    {
        $count = UserQueriesTest::queries()->queryCount(false);
        $this->assertTrue($count > 0);
        $count2 = UserQueriesTest::queries()->queryCount(true);
        $this->assertTrue($count2 > 0);
        $this->assertNotEquals($count,  $count2);
    }
}

//    public function testqueryAll(int $limit = PHP_INT_MAX, int $offset = 0, int $orderby = OrderBy::EMAIL_ASC, bool $excludeAdmins = false): array
//    public function testupdateAlias(int $user_id, string $alias)
//    public function testupdateContactDelay(int $user_id, string $delay): bool
//    public function testupdateContactEmail(int $user_id, string $contact_email): bool
//    public function testupdateLogTime(int $user_id): bool
//    public function testupdatePassword(int $user_id, string $new_password): bool