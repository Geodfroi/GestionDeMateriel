<?php

declare(strict_types=1);

################################
## Joël Piguet - 2021.12.21 ###
##############################

use app\constants\AppPaths;
use app\constants\ArtFilter;
use app\constants\LogChannel;
use app\constants\OrderBy;
use app\helpers\App;
use app\helpers\db\ArticleQueries;
use app\helpers\Logging;
use app\helpers\TestClass;
use app\helpers\TestUtil;
use app\models\Article;

use function PHPUnit\Framework\assertNotNull;
use function PHPUnit\Framework\assertNotSame;
use function PHPUnit\Framework\assertSame;
use function PHPUnit\Framework\assertTrue;
use PHPUnit\Framework\TestCase;

/**
 * https://phpunit.readthedocs.io/en/9.5/writing-tests-for-phpunit.html
 * cmd: ./vendor/bin/phpunit --bootstrap vendor/autoload.php --testdox tests
 */
final class ArticleQueriesTest extends TestCase
{
    /**
     * Set up and access local test db.
     */
    private static function queries()
    {
        static $instance;
        if (is_null($instance)) {
            $local_path = AppPaths::TEST_DB_FOLDER . DIRECTORY_SEPARATOR  . 'testArticles.db';
            $db = TestUtil::localDBSetup($local_path);
            $instance = new ArticleQueries($db);
        }
        return $instance;
    }

    public static function setUpBeforeClass(): void
    {
        App::setConfig(LogChannel::TEST, true, true);
    }

    public static function tearDownAfterClass(): void
    {
        // Database::close();
        // unlink(AppPaths::TEST_UNIT_DB);
    }

    public function testDelete()
    {
        $count = ArticleQueriesTest::queries()->queryCount([ArtFilter::SHOW_EXPIRED => true]);
        // Logging::info('testDelete', ['queryCount' => strval($count)]);
        assertTrue(ArticleQueriesTest::queries()->delete(2));

        assertSame($count - 1, ArticleQueriesTest::queries()->queryCount([ArtFilter::SHOW_EXPIRED => true]));
    }

    public function testInsert()
    {
        $count = ArticleQueriesTest::queries()->queryCount([ArtFilter::SHOW_EXPIRED => true]);
        // Logging::info('testInsert', ['queryCount' => strval($count)]);

        $article = Article::fromForm(0, 'Pneu', 'Garage', '2021-12-19');
        $id = ArticleQueriesTest::queries()->insert($article);
        assertNotSame($id, 0);

        assertSame($count + 1, ArticleQueriesTest::queries()->queryCount([ArtFilter::SHOW_EXPIRED => true]));
    }

    public function testQueryById(): Article
    {
        $article = ArticleQueriesTest::queries()->queryById(1);
        assertNotNull($article);
        return $article;
        // Logging::debug($article->__toString());
    }

    public function testQueryCount(): void
    {
        $count = ArticleQueriesTest::queries()->queryCount([ArtFilter::SHOW_EXPIRED => true]);
        $this->assertTrue($count > 0);
        // Logging::info('testQueryCount', ['count' => strval($count)]);
    }

    /**
     * @dataProvider queryAllProvider
     */
    public function testQueryAll(array $filters): void
    {
        $array = ArticleQueriesTest::queries()->queryAll(PHP_INT_MAX, 0, OrderBy::DELAY_ASC, $filters);
        foreach ($array as $art) {
            Logging::debug($art->__tostring());
        }
        Logging::debug('testQueryAll', ['count' => strval(count($array))]);
        $this->assertNotTrue(count($array) === 0);
    }

    public function queryAllProvider(): array
    {
        return [
            // [[ArtFilter::SHOW_EXPIRED => true]],
            // [[ArtFilter::NAME => 'Pro']],
            [[ArtFilter::DATE_AFTER => '2022-01-01']],
            // [[ArtFilter::DATE_BEFORE => '2021-12-30']],
            // [[
            //     ArtFilter::DATE_AFTER => '2021-12-30',
            //     ArtFilter::NAME => 'Pro'
            // ]],
        ];
    }

    /**
     * @depends testQueryById
     */
    public function testUpdate($article): void
    {
        $this->assertTrue(ArticleQueriesTest::queries()->update($article));
    }
}
