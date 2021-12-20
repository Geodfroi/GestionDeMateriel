<?php

declare(strict_types=1);

################################
## Joël Piguet - 2021.12.20 ###
##############################

use app\constants\ArtFilter;
use app\constants\LogChannel;
use app\constants\Globals;
use app\helpers\Logging;
use app\helpers\TestClass;
use app\helpers\Database;
use app\helpers\TestUtil;
use app\models\Article;

use function PHPUnit\Framework\assertFalse;
use function PHPUnit\Framework\assertNotNull;
use function PHPUnit\Framework\assertNotSame;
use function PHPUnit\Framework\assertSame;
use function PHPUnit\Framework\assertTrue;

/**
 * https://phpunit.readthedocs.io/en/9.5/writing-tests-for-phpunit.html
 * cmd: ./vendor/bin/phpunit --bootstrap vendor/autoload.php --testdox tests
 */
final class ArticleQueriesTest extends TestClass
{
    /**
     * @beforeClass
     */
    public static function beforeAll(): void
    {
        // Logging::error('beforeall');
        $GLOBALS[Globals::LOG_CHANNEL] = LogChannel::TEST;
        $GLOBALS[Globals::DATABASE] = Globals::USE_UNIT_TEST;

        TestUtil::populateSQLiteDB();
    }

    public function testT(): void
    {
        assertTrue(True);
    }

    // public function testDelete()
    // {
    //     assertTrue(Database::articles()->delete(2));
    // }

    /**
     * @depends testQueryCount
     */
    public function testInsert()
    {
        $count = Database::articles()->queryCount([ArtFilter::SHOW_EXPIRED => true]);
        Logging::info('testInsert', ['queryCount' => strval($count)]);

        $article = Article::fromForm(0, 'Pneu', 'Garage', '2021-12-19');
        $id = Database::articles()->insert($article);
        assertNotSame($id, 0);

        $count2 = Database::articles()->queryCount([ArtFilter::SHOW_EXPIRED => true]);

        assertSame($count2, $count + 1);
    }

    // public function testQueryById(): Article
    // {
    //     $article = Database::articles()->queryById(1);
    //     assertNotNull($article);
    //     return $article;
    //     // Logging::debug($article->__toString());
    // }

    public function testQueryCount(): void
    {
        $count = Database::articles()->queryCount([ArtFilter::SHOW_EXPIRED => true]);
        $this->assertTrue($count > 0);
        Logging::info('testQueryCount', ['count' => strval($count)]);
    }

    // public function testQueryAll(): void
    // {
    //     $array = Database::articles()->queryAll();
    //     // Logging::debug('testQueryAll', $array);
    //     $this->assertNotTrue(count($array) === 0);
    // }

    // /**
    //  * @depends testQueryById
    //  */
    // public function testUpdate(): void
    // {
    //     $article = $this->testQueryById();
    //     $this->assertTrue(Database::articles()->update($article));
    // }
}
