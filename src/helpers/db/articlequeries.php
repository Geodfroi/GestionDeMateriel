<?php

declare(strict_types=1);

################################
## Joël Piguet - 2021.12.07 ###
##############################

namespace app\helpers\db;

use \PDO;

use app\constants\Error;
use app\constants\Filter;
use app\constants\OrderBy;
use app\models\Article;
use DateTime;

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
     * Get number of articles in db.
     * 
     * @param int $filter_type Filter parameter. Use Filter constants as parameter.
     * @param string $filter_arg Filter argument value.
     * @return int # of articles or -1 if query fails.
     */
    public function queryCount(int $filter_type = Filter::ARTICLE_NAME, $filter_arg = ''): int
    {
        $filter_arg = trim($filter_arg);
        $filter_statement = $filter_arg ? Filter::printStatement($filter_type) : '';

        error_log('count: ' . "SELECT COUNT(*) FROM articles $filter_statement");
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
     * Retrieve database articles;
     * 
     * @param int $limit The maximum number of items to be returned.
     * @param int $offset The number of result items to be skipped before including them to the result array.
     * @param int $orderby Order parameter. Use OrderBy constants as parameter.
     * @param int $filter_type Filter parameter. Use Filter constants as parameter.
     * @param string $filter_arg Filter argument value.
     * @return array An array of articles.
     */
    public function queryAll(int $limit = PHP_INT_MAX, int $offset = 0, int $orderby = OrderBy::DATE_DESC, int $filter_type = Filter::ARTICLE_NAME, $filter_arg = ''): array
    {
        $filter_arg = trim($filter_arg);
        $filter_statement = $filter_arg ? Filter::printStatement($filter_type) : '';
        $order_q = OrderBy::getOrderParameters($orderby);

        $preparedStatement = $this->pdo->prepare("SELECT 
            id, 
            user_id, 
            article_name, 
            location,
            comments, 
            expiration_date,
            creation_date
        FROM articles 
        $filter_statement
        ORDER BY $order_q LIMIT :lim OFFSET :off");

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
