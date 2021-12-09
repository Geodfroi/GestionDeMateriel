<?php

declare(strict_types=1);

################################
## Joël Piguet - 2021.12.09 ###
##############################

namespace app\helpers\db;

use app\constants\Error;
use app\constants\Filter;
use app\constants\OrderBy;
use app\models\Article;

use Exception;
use \PDO;

/**
 * Regroup function to interact with article table.
 */
class ArticleQueries
{
    private PDO $pdo;

    function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Delete the article from db by id.
     * 
     * @param int $id Article id.
     * @return bool True if the delete is successful.
     */
    public function delete(int $id): bool
    {
        $preparedStatement = $this->pdo->prepare('DELETE FROM articles WHERE id = :id');
        $preparedStatement->bindParam(':id', $id, PDO::PARAM_INT);

        if ($preparedStatement->execute()) {
            return true;
        }
        list(,, $error) = $preparedStatement->errorInfo();
        error_log(error::ARTICLE_DELETE . $error . PHP_EOL);
        return false;
    }

    /**
     * Delete all articles belonging to user.
     * 
     * @param int $user_id The user id.
     * @return bool True if the delete is successful.
     */
    public function deleteUserArticles($user_id): bool
    {
        $preparedStatement = $this->pdo->prepare('DELETE FROM articles WHERE user_id = :uid');
        $preparedStatement->bindParam(':uid', $user_id, PDO::PARAM_INT);
        if ($preparedStatement->execute()) {
            return true;
        }
        list(,, $error) = $preparedStatement->errorInfo();
        error_log(Error::USER_ARTICLES_DELETE . $error . PHP_EOL);
        return false;
    }


    /**
     * Compose WHERE clause to be inserted into article database query.
     * 
     * @param int $param Filter constant value.
     * @return string Where clause.
     */
    public static function printFilterStatement($param): string
    {
        switch ($param) {
            case Filter::ARTICLE_NAME:
                return "WHERE article_name LIKE CONCAT('%', :fil, '%')";
            case Filter::LOCATION:
                return "WHERE location LIKE CONCAT('%', :fil, '%')";
            case Filter::DATE_BEFORE:
                return "WHERE expiration_date < :fil";
            case Filter::DATE_AFTER:
                return "WHERE expiration_date > :fil";
            default:
                break;
        }
        throw new Exception("printStatement:: Invalid [$param] parameter");
    }

    /**
     * Compose ORDER BY clause.
     * 
     * @param int $param OrderBy constant value.
     * @return string orderby clause.
     */
    public static function printOrderStatement(int $param): string
    {
        // CURRENT_TIMESTAMP - expiration_date <- Order old articles at the end.

        switch ($param) {
                // order by is already expired, then name, then delay until peremption by order of urgency.
            case OrderBy::NAME_ASC:
                return 'ORDER BY (CURRENT_TIMESTAMP > expiration_date), article_name ASC, (CURRENT_TIMESTAMP - expiration_date) DESC';
            case OrderBy::NAME_DESC:
                return 'ORDER BY (CURRENT_TIMESTAMP > expiration_date), article_name DESC, (CURRENT_TIMESTAMP - expiration_date) DESC';

                // order by is already expired, then location, then delay until peremption by order of urgency.
            case OrderBy::LOCATION_ASC:
                return 'ORDER BY (CURRENT_TIMESTAMP > expiration_date), location ASC, (CURRENT_TIMESTAMP - expiration_date) DESC';
            case OrderBy::LOCATION_DESC:
                return 'ORDER BY (CURRENT_TIMESTAMP > expiration_date), location DESC, (CURRENT_TIMESTAMP - expiration_date) DESC';
                // order by is already expired, then delay, then owner.
            case OrderBy::DELAY_ASC:
                return 'ORDER BY (CURRENT_TIMESTAMP > expiration_date), (CURRENT_TIMESTAMP - expiration_date) ASC, alias ASC';
            case OrderBy::DELAY_DESC:
                return 'ORDER BY (CURRENT_TIMESTAMP > expiration_date), (CURRENT_TIMESTAMP - expiration_date) DESC, alias ASC';

                // order by is already expired, then owner, then creation date, then delay.
            case OrderBy::OWNED_BY_ASC:
                return 'ORDER BY (CURRENT_TIMESTAMP > expiration_date), alias ASC, creation_date ASC, (CURRENT_TIMESTAMP - expiration_date) DESC';
            case OrderBy::OWNED_BY_DESC:
                return 'ORDER BY (CURRENT_TIMESTAMP > expiration_date), alias DESC, creation_date ASC, (CURRENT_TIMESTAMP - expiration_date) DESC';
            default:
                break;
        }
        throw new Exception("printOrderStatement: invalid [$param)] parameter");
    }

    /**
     * Insert an article into the database.
     * 
     * @param Article $article Article to insert.
     * @return bool True if the insert is successful.
     */
    public function insert(Article $article): bool
    {
        $preparedStatement = $this->pdo->prepare(
            'INSERT INTO articles 
            (
                user_id, 
                article_name, 
                location, 
                expiration_date
            ) 
            VALUES 
            (
                :uid, 
                :art, 
                :loc, 
                :date
            )'
        );

        $uid = $article->getUserId();
        $name = $article->getArticleName();
        $location = $article->getLocation();
        $date = $article->getExpirationDate()->format('Y-m-d H:i:s');

        $preparedStatement->bindParam(':uid', $uid, PDO::PARAM_INT);
        $preparedStatement->bindParam(':art', $name, PDO::PARAM_STR);
        $preparedStatement->bindParam(':loc', $location, PDO::PARAM_STR);
        $preparedStatement->bindParam(':date', $date, PDO::PARAM_STR);

        if ($preparedStatement->execute()) {
            return true;
        }
        list(,, $error) = $preparedStatement->errorInfo();
        error_log(Error::ARTICLE_INSERT . $error . PHP_EOL);
        return false;
    }

    /**
     * Retrieve a single Article by id.
     * 
     * @param int $id Article id.
     * @return Article|null
     */
    public function queryById(int $id): ?Article
    {
        $preparedStatement = $this->pdo->prepare('SELECT 
            id, 
            user_id, 
            article_name, 
            location,
            comments, 
            expiration_date,
            creation_date 
        FROM articles WHERE id = :id');

        $preparedStatement->bindParam(':id', $id, PDO::PARAM_INT);

        if ($preparedStatement->execute()) {

            $data = $preparedStatement->fetch(); // retrieve only first row found; fine since id is unique.
            if ($data) {
                return Article::fromDatabaseRow($data);
            } else {
                error_log(sprintf(Error::ARTICLE_QUERY, $id));
            }
        } else {
            list(,, $error) = $preparedStatement->errorInfo();
            error_log(sprintf(Error::ARTICLE_QUERY, $id) . $error . PHP_EOL);
        }

        return null;
    }

    /**
     * Get number of articles in db.
     * 
     * @param int $filter_type Filter parameter. Use Filter constants as parameter.
     * @param string $filter_arg Filter argument value.
     * @return int # of articles or -1 if query fails.
     */
    public function queryCount(int $filter_type = Filter::ARTICLE_NAME, $filter_arg = ''): int
    {
        $filter_arg = trim($filter_arg);
        $filter_statement = $filter_arg ? ArticleQueries::printOrderStatement($filter_type) : '';

        $preparedStatement = $this->pdo->prepare("SELECT COUNT(*) FROM articles $filter_statement");

        if ($filter_arg) {
            $preparedStatement->bindParam(':fil', $filter_arg, PDO::PARAM_STR);
        }

        if ($preparedStatement->execute()) {
            $r = $preparedStatement->fetchColumn();
            return intval($r);
        }

        list(,, $error) = $preparedStatement->errorInfo();
        error_log(Error::ARTICLES_COUNT_QUERY . $error . PHP_EOL);
        return -1;
    }

    /**
     * Retrieve database articles;
     * 
     * @param int $limit The maximum number of items to be returned.
     * @param int $offset The number of result items to be skipped before including them to the result array.
     * @param int $orderby Order parameter. Use OrderBy constants as parameter.
     * @param int $filter_type Filter parameter. Use Filter constants as parameter.
     * @param string $filter_arg Filter argument value.
     * @return array An array of articles.
     */
    public function queryAll(int $limit = PHP_INT_MAX, int $offset = 0, int $orderby = OrderBy::DELAY_ASC, int $filter_type = Filter::ARTICLE_NAME, $filter_arg = ''): array
    {
        $filter_arg = trim($filter_arg);

        $filter_statement = $filter_arg ? ArticleQueries::printOrderStatement($filter_type) : '';
        $order_statement = ArticleQueries::printOrderStatement($orderby);

        // join table if necessary to have user column alias available in orderby statements.
        $preparedStatement = $this->pdo->prepare("SELECT 
            articles.id, 
            articles.user_id, 
            articles.article_name, 
            articles.location,
            articles.comments, 
            articles.expiration_date,
            articles.creation_date,
            users.alias
        FROM articles INNER JOIN users ON articles.user_id = users.id
        $filter_statement
        $order_statement
        LIMIT :lim OFFSET :off");

        if ($filter_arg) {
            $preparedStatement->bindParam(':fil', $filter_arg, PDO::PARAM_STR);
        }
        $preparedStatement->bindParam(':lim', $limit, PDO::PARAM_INT);
        $preparedStatement->bindParam(':off', $offset, PDO::PARAM_INT);

        $articles = [];

        if ($preparedStatement->execute()) {
            // fetch next as associative array until there are none to be fetched.
            while ($data = $preparedStatement->fetch()) {

                array_push($articles, Article::fromDatabaseRow($data));
            }
        } else {
            list(,, $error) = $preparedStatement->errorInfo();
            error_log(Error::ARTICLES_QUERY . $error . PHP_EOL);
        }

        return $articles;
    }

    /**
     * Update article in database.
     * 
     * @param Article Article to be updated.
     * @return bool True if update is successful.
     */
    public function update(Article $article): bool
    {
        $preparedStatement = $this->pdo->prepare('UPDATE articles SET
            article_name = :name,
            location = :loc,
            expiration_date = :date,
            comments = :com
        WHERE id = :id');

        $name = $article->getArticleName();
        $location = $article->getLocation();
        $date = $article->getExpirationDate()->format('Y-m-d H:i:s');
        $comments = $article->getComments();
        $id = $article->getId();

        $preparedStatement->bindParam(':name', $name, PDO::PARAM_STR);
        $preparedStatement->bindParam(':loc', $location, PDO::PARAM_STR);
        $preparedStatement->bindParam(':date', $date, PDO::PARAM_STR);
        $preparedStatement->bindParam(':com', $comments, PDO::PARAM_STR);
        $preparedStatement->bindParam(':id', $id, PDO::PARAM_INT);

        if ($preparedStatement->execute()) {
            return true;
        }
        list(,, $error) = $preparedStatement->errorInfo();
        error_log('failure to update article: ' . $error . PHP_EOL);
        return false;
    }
}
