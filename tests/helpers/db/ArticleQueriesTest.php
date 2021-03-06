<?php

declare(strict_types=1);

################################
## Joël Piguet - 2022.03.11 ###
##############################


use app\constants\ArtFilter;
use app\constants\OrderBy;
use app\helpers\db\ArticleQueries;
use app\models\Article;
use app\tests\TestClass;


use function PHPUnit\Framework\assertNotSame;
use function PHPUnit\Framework\assertSame;
use function PHPUnit\Framework\assertTrue;

/**
 * https://phpunit.readthedocs.io/en/9.5/writing-tests-for-phpunit.html
 * cmd: ./vendor/bin/phpunit --bootstrap vendor/autoload.php --testdox tests
 */
final class ArticleQueriesTest extends TestClass
{
    private static ArticleQueries $queries;

    public static function setUpBeforeClass(): void
    {
        ArticleQueriesTest::$queries = new ArticleQueries(ArticleQueriesTest::getConn());
    }

    public static function tearDownAfterClass(): void
    {
    }

    public static function testTrue()
    {
        assertTrue(true);
    }

    // public static function testBackup()
    // {
    //     $backup_conn = DBUtil::localDBSetup(AppPaths::TEST_DB_FOLDER, 'articles_backup', false);
    //     assertNotNull($backup_conn);
    //     assertTrue(ArticleQueriesTest::$queries->backup($backup_conn));
    // }

    public function testInsert()
    {
        $article = Article::fromForm(0, 'Pneu', 'Garage', '2021-12-19');
        $id = ArticleQueriesTest::$queries->insert($article);
        assertNotSame($id, 0);
        assertSame(ArticleQueriesTest::$queries->queryById($id)->getArticleName(), 'Pneu');
    }

    public function testInsertDelete()
    {
        $article = Article::fromForm(0, 'Carambole', 'Armoire à jeux', '2022-01-08');
        $id = ArticleQueriesTest::$queries->insert($article);
        assertNotSame($id, 0);
        assertTrue(ArticleQueriesTest::$queries->delete($id));
    }

    public function testQueryById()
    {
        $article = Article::fromForm(0, 'Pied de biche', 'Garage', '2022-01-10');
        $id = ArticleQueriesTest::$queries->insert($article);

        $article = ArticleQueriesTest::$queries->queryById($id);
        assertSame($article->getLocation(), 'Garage');
    }

    public function testQueryCount(): void
    {
        $article = Article::fromForm(0, 'Colle', 'trousse', '2022-01-11');
        ArticleQueriesTest::$queries->insert($article);
        $article = Article::fromForm(0, 'Assiette x10', 'armoire', '2022-01-12');
        ArticleQueriesTest::$queries->insert($article);

        $count = ArticleQueriesTest::$queries->queryCount([ArtFilter::SHOW_EXPIRED => true]);
        $this->assertTrue($count > 2);
    }

    /**
     * @depends testQueryById
     */
    public function testUpdate($article): void
    {
        $article = Article::fromForm(0, 'Colle', 'trousse', '2022-01-11');
        ArticleQueriesTest::$queries->insert($article);

        $this->assertTrue(ArticleQueriesTest::$queries->update($article));
    }

    public function queryAllProvider(): array
    {
        return [
            [[], OrderBy::DELAY_ASC],
            [[], OrderBy::OWNED_BY_DESC],
            [[ArtFilter::SHOW_EXPIRED => true], OrderBy::DELAY_ASC],
            [[ArtFilter::NAME => 'Pro'], OrderBy::DELAY_ASC],
            [[ArtFilter::DATE_AFTER => '2022-01-01'], OrderBy::DELAY_ASC],
            [[
                ArtFilter::DATE_AFTER => '2022-01-01',
                ArtFilter::NAME => 'Pro'
            ], OrderBy::DELAY_ASC],
            [[ArtFilter::DATE_BEFORE => '2022-01-01'], OrderBy::DELAY_ASC],
        ];
    }

    /**
     * @dataProvider queryAllProvider
     */
    public function testQueryAll(array $filters, string $order_by): void
    {
        $array = ArticleQueriesTest::$queries->queryAll(PHP_INT_MAX, 0, $order_by, $filters);
        // assert($array);
        // foreach ($array as $art) {
        //     Logging::debug($art->__tostring());
        // }
        // Logging::debug('testQueryAll', ['count' => strval(count($array))]);
        $this->assertNotTrue(count($array) === 0);
    }
}
