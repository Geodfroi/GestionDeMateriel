<?php

declare(strict_types=1);

################################
## Joël Piguet - 2021.12.13 ###
##############################

namespace app\helpers\db;

use Exception;
use \PDO;

use app\constants\ArtFilter;
use app\constants\LogError;
use app\constants\OrderBy;
use app\helpers\Logging;
use app\helpers\Util;
use app\models\Article;

/**
 * Regroup function to interact with article table.
 */
class ArticleQueries
{
    private PDO $pdo;
    private string $logger;

    function __construct(PDO $pdo, string $logger)
    {
        $this->pdo = $pdo;
        $this->logger = $logger;
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
        Logging::error(LogError::ARTICLE_DELETE, ['error' => $error], $this->logger);
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
        Logging::error(LogError::USER_ARTICLES_DELETE, ['error' => $error], $this->logger);
        return false;
    }

    /**
     *  Compose WHERE clause to be inserted into article database query; can be composed of several filters.
     * 
     * @param array $filters Array of ArtFilter Instances
     * @return string Where clause.
     */
    public static function printFilterStatement(array $filters): string
    {
        $str = 'WHERE ';
        $count = 0;

        //is key-value set and value not null or empty
        if (isset($filters[ArtFilter::NAME]) && $filters[ArtFilter::NAME]) {
            $str .= "article_name LIKE CONCAT ('%', :fname, '%')";
            $count += 1;
        }

        if (isset($filters[ArtFilter::LOCATION]) && $filters[ArtFilter::LOCATION]) {
            if ($count === 1) {
                $str .= ' AND ';
            }
            $str .= "location LIKE CONCAT ('%', :floc, '%')";
            $count += 1;
        }

        if (isset($filters[ArtFilter::DATE_BEFORE]) && $filters[ArtFilter::DATE_BEFORE]) {
            if ($count === 1) {
                $str .= ' AND ';
            }
            $str .= "expiration_date < :fbefore";
            $count += 1;
        } else if (isset($filters[ArtFilter::DATE_AFTER]) && $filters[ArtFilter::DATE_AFTER]) {
            if ($count === 1) {
                $str .= ' AND ';
            }
            $str .= "expiration_date > :fbefore";
            $count += 1;
        }

        if (!isset($filters[ArtFilter::SHOW_EXPIRED])) {

            if ($count === 1) {
                $str .= ' AND ';
            }
            $str .= "(expiration_date > CURRENT_TIMESTAMP)";
            $count += 1;
        }

        return $count > 0 ? $str : '';
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
        Logging::error(LogError::ARTICLE_INSERT, ['error' => $error], $this->logger);
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
                Logging::error(LogError::ARTICLE_QUERY, ['id' => $id], $this->logger);
            }
        } else {
            list(,, $error) = $preparedStatement->errorInfo();
            Logging::error(LogError::ARTICLE_QUERY, ['id' => $id, 'error' => $error], $this->logger);
        }

        return null;
    }

    /**
     * Get number of articles in db.
     * 
     * @param array $filters Array of ArtFilter instances.
     * @return int # of articles or -1 if query fails.
     */
    public function queryCount(array $filters = []): int
    {
        $filter_statement = ArticleQueries::printFilterStatement($filters);
        $preparedStatement = $this->pdo->prepare("SELECT COUNT(*) FROM articles $filter_statement");

        if (Util::str_contains($filter_statement, ':fname')) {
            $preparedStatement->bindParam(':fname', $filters[ArtFilter::NAME], PDO::PARAM_STR);
        }
        if (Util::str_contains($filter_statement, ':floc')) {
            $preparedStatement->bindParam(':floc', $filters[ArtFilter::LOCATION], PDO::PARAM_STR);
        }
        if (Util::str_contains($filter_statement, ':fbefore')) {
            $preparedStatement->bindParam(':fbefore', $filters[ArtFilter::DATE_BEFORE], PDO::PARAM_STR);
        }
        if (Util::str_contains($filter_statement, ':fafter')) {
            $preparedStatement->bindParam(':fafter', $filters[ArtFilter::DATE_AFTER], PDO::PARAM_STR);
        }

        if ($preparedStatement->execute()) {
            $r = $preparedStatement->fetchColumn();
            return intval($r);
        }

        list(,, $error) = $preparedStatement->errorInfo();
        Logging::error(LogError::ARTICLES_COUNT_QUERY, ['error' => $error], $this->logger);
        return -1;
    }

    /**
     * Retrieve database articles;
     * 
     * @param int $limit The maximum number of items to be returned.
     * @param int $offset The number of result items to be skipped before including them to the result array.
     * @param int $orderby Order parameter. Use OrderBy constants as parameter.
     * @param array $filters Array of ArtFilter instances.
     * @return array An array of articles.
     */
    public function queryAll(int $limit = PHP_INT_MAX, int $offset = 0, int $orderby = OrderBy::DELAY_ASC, array $filters = []): array
    {
        $filter_statement = ArticleQueries::printFilterStatement($filters);
        $order_statement = ArticleQueries::printOrderStatement($orderby);

        // Logging::debug($filter_statement);
        // Logging::debug('filters', $filters);

        // Logging::debug("SELECT 
        //     articles.id, 
        //     articles.user_id, 
        //     articles.article_name, 
        //     articles.location,
        //     articles.comments, 
        //     articles.expiration_date,
        //     articles.creation_date,
        //     users.alias
        // FROM articles LEFT JOIN users ON articles.user_id = users.id
        // $filter_statement
        // $order_statement
        // LIMIT :lim OFFSET :off");

        // join table if necessary to have user column alias available in orderby statements.
        // LEFT JOIN: all articles are listed even if the user who created the article is no longer be present in db.
        $preparedStatement = $this->pdo->prepare("SELECT 
            articles.id, 
            articles.user_id, 
            articles.article_name, 
            articles.location,
            articles.comments, 
            articles.expiration_date,
            articles.creation_date,
            users.alias
        FROM articles LEFT JOIN users ON articles.user_id = users.id 
        $filter_statement 
        $order_statement
        LIMIT :lim OFFSET :off");

        if (Util::str_contains($filter_statement, ':fname')) {
            $preparedStatement->bindParam(':fname', $filters[ArtFilter::NAME], PDO::PARAM_STR);
        }
        if (Util::str_contains($filter_statement, ':floc')) {
            $preparedStatement->bindParam(':floc', $filters[ArtFilter::LOCATION], PDO::PARAM_STR);
        }
        if (Util::str_contains($filter_statement, ':fbefore')) {
            $preparedStatement->bindParam(':fbefore', $filters[ArtFilter::DATE_BEFORE], PDO::PARAM_STR);
        }
        if (Util::str_contains($filter_statement, ':fafter')) {
            $preparedStatement->bindParam(':fafter', $filters[ArtFilter::DATE_AFTER], PDO::PARAM_STR);
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
            Logging::error(LogError::ARTICLES_QUERY, ['error' => $error], $this->logger);
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
        Logging::error(LogError::ARTICLE_UPDATE, ['id' => $id, 'error' => $error], $this->logger);
        return false;
    }
}
