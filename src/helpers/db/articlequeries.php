<?php

declare(strict_types=1);

################################
## Joël Piguet - 2022.01.09 ###
##############################

namespace app\helpers\db;

use Exception;
use SQLite3;

use app\constants\ArtFilter;
use app\constants\LogError;
use app\constants\OrderBy;
use app\helpers\App;
use app\helpers\Logging;
use app\helpers\Util;
use app\models\Article;

/**
 * Regroup function to interact with article table.
 */
class ArticleQueries extends Queries
{
    /**
     * Transfer article rows to local sqlite db.
     * 
     * @param SQlite3 $backup_conn Db backup connection.
     * @return True if backup is successful.
     */
    public function backup(SQlite3 $backup_conn): bool
    {
        $query_stmt = $this->conn->prepare("SELECT * FROM articles");
        $r = $query_stmt->execute();

        while ($row = $this->fetchRow($r, $query_stmt)) {
            $id  = (int)($row['id'] ?? 0);
            $user_id = (int)($row['user_id'] ?? 0);
            $article_name = (string)($row['article_name'] ?? '');
            $location = (string)($row['location'] ?? '');
            $comments = (string)($row['comments'] ?? '');
            $expiration_date = (string)$row['expiration_date'];
            $creation_date = (string)$row['creation_date'];

            $insert_stmt = $backup_conn->prepare('INSERT INTO articles 
            (   
                id,
                user_id, 
                article_name, 
                location, 
                comments,
                expiration_date,
                creation_date
            ) 
            VALUES 
            (
                :id,
                :user_id,  
                :article_name,  
                :location, 
                :comments,
                :expiration_date,
                :creation_date
            )');

            $insert_stmt->bindParam(':id', $id, SQLITE3_INTEGER);
            $insert_stmt->bindParam(':user_id', $user_id, SQLITE3_INTEGER);
            $insert_stmt->bindParam(':article_name', $article_name, SQLITE3_TEXT);
            $insert_stmt->bindParam(':location', $location, SQLITE3_TEXT);
            $insert_stmt->bindParam(':comments', $comments, SQLITE3_TEXT);
            $insert_stmt->bindParam(':expiration_date', $expiration_date, SQLITE3_TEXT);
            $insert_stmt->bindParam(':creation_date', $creation_date, SQLITE3_TEXT);

            if (!$insert_stmt->execute()) {
                Logging::error('failure to insert article in backup db', ['article' => $article_name]);
                return false;
            };
        }
        return true;
    }

    /**
     * Delete the article from db by id.
     * 
     * @param int $id Article id.
     * @return bool True if the delete is successful.
     */
    public function delete(int $id): bool
    {
        $stmt = $this->conn->prepare('DELETE FROM articles WHERE id = :id');
        $stmt->bindParam(':id', $id, $this->data_types['int']);

        if ($stmt->execute()) {
            return true;
        }
        Logging::error(LogError::ARTICLE_DELETE, ['error' => $this->error($stmt)]);
        return false;
    }

    /**
     *  Compose WHERE clause to be inserted into article database query; can be composed of several filters. SQLite LIKE statements don't use prepared statements and therefore aren't properly escaped. It doesn't matter as SQLITE is only for testing.
     * 
     * @param array $filters Array of ArtFilter Instances
     * @return string Where clause.
     */
    public function printFilterStatement(array $filters): string
    {
        $str = 'WHERE ';
        $count = 0;

        //is key-value set and value not null or empty
        if (isset($filters[ArtFilter::NAME]) && $filters[ArtFilter::NAME]) {
            $str .= App::useSQLite() ? "(article_name LIKE '%{$filters[ArtFilter::NAME]}%')" : "article_name LIKE CONCAT ('%', :fname, '%')";
            $count += 1;
        }

        if (isset($filters[ArtFilter::LOCATION]) && $filters[ArtFilter::LOCATION]) {
            if ($count > 1) {
                $str .= ' AND ';
            }
            $str .= App::useSQLite() ? "(location LIKE '%{$filters[ArtFilter::LOCATION]}%')" : "location LIKE CONCAT ('%', :floc, '%')";
            $count += 1;
        }

        if (isset($filters[ArtFilter::DATE_BEFORE]) && $filters[ArtFilter::DATE_BEFORE]) {
            if ($count > 1) {
                $str .= ' AND ';
            }
            $str .= App::useSQLite() ? "(expiration_date < '{$filters[ArtFilter::DATE_BEFORE]}')" : "expiration_date < :fbefore";
            $count += 1;
        } else if (isset($filters[ArtFilter::DATE_AFTER]) && $filters[ArtFilter::DATE_AFTER]) {
            if ($count > 0) {
                $str .= ' AND ';
            }
            $str .= App::useSQLite() ? "(expiration_date > '{$filters[ArtFilter::DATE_AFTER]}')" : "expiration_date > :fafter";
            $count += 1;
        }

        if (!isset($filters[ArtFilter::SHOW_EXPIRED])) {

            // Logging::debug('printFilterStatement');
            if ($count > 0) {
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
                return 'ORDER BY (CURRENT_TIMESTAMP > expiration_date), alias ASC, users.creation_date ASC, (CURRENT_TIMESTAMP - expiration_date) DESC';
            case OrderBy::OWNED_BY_DESC:
                return 'ORDER BY (CURRENT_TIMESTAMP > expiration_date), alias DESC, users.creation_date ASC, (CURRENT_TIMESTAMP - expiration_date) DESC';
            default:
                break;
        }
        throw new Exception("printOrderStatement: invalid [$param)] parameter");
    }

    /**
     * Insert an article into the database.
     * 
     * @param Article $article Article to insert.
     * @return int ID of inserted row or 0 if it fails.
     */
    public function insert(Article $article): int
    {
        $stmt = $this->conn->prepare(
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

        $stmt->bindParam(':uid', $uid, $this->data_types['int']);
        $stmt->bindParam(':art', $name, $this->data_types['str']);
        $stmt->bindParam(':loc', $location, $this->data_types['str']);
        $stmt->bindParam(':date', $date, $this->data_types['str']);

        $r = $stmt->execute();
        if ($r) {
            $id = $this->rowId();
            $article->setId($id);
            return $id;
        }
        Logging::error(LogError::ARTICLE_INSERT, ['error' => $this->error($stmt)]);
        return 0;
    }

    /**
     * Retrieve a single Article by id.
     * 
     * @param int $id Article id.
     * @return Article|null
     */
    public function queryById(int $id): ?Article
    {
        $stmt = $this->conn->prepare('SELECT 
            id, 
            user_id, 
            article_name, 
            location,
            comments, 
            expiration_date,
            creation_date 
        FROM articles WHERE id = :id');

        $stmt->bindParam(':id', $id, $this->data_types['int']);

        $r = $stmt->execute();
        if ($r) {
            // retrieve only first row found; fine since id is unique.
            $row = $this->fetchRow($r, $stmt);
            if ($row) {
                return Article::fromDatabaseRow($row);
            }
            Logging::error(LogError::ARTICLE_QUERY, ['id' => $id]);
            return null;
        }
        Logging::error(LogError::ARTICLE_QUERY, ['id' => $id, 'error' => $this->error($stmt)]);
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
        $filter_statement = $this->printFilterStatement($filters);
        $query = "SELECT COUNT(*) FROM articles $filter_statement";

        // Logging::debug('queryCount stmt: ' . $query);
        $stmt = $this->conn->prepare($query);

        if (Util::str_contains($filter_statement, ':fname')) {
            $stmt->bindParam(':fname', $filters[ArtFilter::NAME], $this->data_types['str']);
        }
        if (Util::str_contains($filter_statement, ':floc')) {
            $stmt->bindParam(':floc', $filters[ArtFilter::LOCATION], $this->data_types['str']);
        }
        if (Util::str_contains($filter_statement, ':fbefore')) {
            $stmt->bindParam(':fbefore', $filters[ArtFilter::DATE_BEFORE], $this->data_types['str']);
        }
        if (Util::str_contains($filter_statement, ':fafter')) {
            $stmt->bindParam(':fafter', $filters[ArtFilter::DATE_AFTER], $this->data_types['str']);
        }

        $r = $stmt->execute();
        if ($r) {
            return $this->count($r, $stmt);
        }

        Logging::error(LogError::ARTICLES_COUNT_QUERY, ['error' => $this->error($stmt)]);
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
        // Logging::debug('queryAll', $filters);

        $filter_statement = $this->printFilterStatement($filters);
        $order_statement = ArticleQueries::printOrderStatement($orderby);

        $query = "SELECT 
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
        LIMIT :lim OFFSET :off";

        // Logging::debug('queryAll', ['order_statement' => $order_statement]);
        // Logging::debug('queryAll', ['filter_statement' => $filter_statement]);
        // Logging::debug('queryAll', ['query' => $query]);

        // join table if necessary to have user column alias available in orderby statements.
        // LEFT JOIN: all articles are listed even if the user who created the article is no longer be present in db.
        $stmt = $this->conn->prepare($query);

        if (Util::str_contains($filter_statement, ':fname')) {
            $stmt->bindParam(':fname', $filters[ArtFilter::NAME], $this->data_types['str']);
        }
        if (Util::str_contains($filter_statement, ':floc')) {
            $stmt->bindParam(':floc', $filters[ArtFilter::LOCATION], $this->data_types['str']);
        }
        if (Util::str_contains($filter_statement, ':fbefore')) {
            $stmt->bindParam(':fbefore', $filters[ArtFilter::DATE_BEFORE], $this->data_types['str']);
        }
        if (Util::str_contains($filter_statement, ':fafter')) {
            Logging::debug(':fafter');
            $stmt->bindParam(':fafter', $filters[ArtFilter::DATE_AFTER], $this->data_types['str']);
        }

        $stmt->bindParam(':lim', $limit, $this->data_types['int']);
        $stmt->bindParam(':off', $offset, $this->data_types['int']);

        $r = $stmt->execute();
        if ($r) {
            $articles = [];
            // fetch next as associative array until there are none to be fetched.
            while ($row = $this->fetchRow($r, $stmt)) {
                array_push($articles, Article::fromDatabaseRow($row));
            }
            return $articles;
        }

        Logging::error(LogError::ARTICLES_QUERY, ['error' => $this->error($stmt)]);
        return [];
    }

    /**
     * Update article in database.
     * 
     * @param Article Article to be updated.
     * @return bool True if update is successful.
     */
    public function update(Article $article): bool
    {
        $stmt = $this->conn->prepare('UPDATE articles SET
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

        $stmt->bindParam(':name', $name, $this->data_types['str']);
        $stmt->bindParam(':loc', $location, $this->data_types['str']);
        $stmt->bindParam(':date', $date, $this->data_types['str']);
        $stmt->bindParam(':com', $comments, $this->data_types['str']);
        $stmt->bindParam(':id', $id, $this->data_types['int']);

        if ($stmt->execute()) {
            return true;
        }

        Logging::error(LogError::ARTICLE_UPDATE, [
            'id' => $id,
            'error' => $this->error($stmt)
        ]);
        return false;
    }
}
