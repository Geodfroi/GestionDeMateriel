<?php

declare(strict_types=1);

################################
## Joël Piguet - 2021.12.20 ###
##############################

namespace app\helpers\db;

use Exception;
use \PDO;
use SQLite3;

use app\constants\ArtFilter;
use app\constants\LogError;
use app\constants\OrderBy;
use app\helpers\Logging;
use app\helpers\Util;
use app\models\Article;
use app\helpers\Database;

/**
 * Regroup function to interact with article table.
 */
class ArticleQueries
{
    private $conn;
    private bool $use_sqlite;
    private array $data_types;

    /**
     * @param PDO|SQlite3 $conn Db connection.
     * @param bool $use_sqlite Set for sqlite queries instead of MySQL.
     */
    function __construct(Database $db)
    {
        $this->conn = $db->getConn();
        $this->use_sqlite = $db->useSQLite();
        $this->data_types = $db->getDataTypes();
    }

    public function backup()
    {
        Logging::debug('article backup not implemented');

        // $table_name = "employee";
        // $backup_file  = "/tmp/employee.sql";
        // $sql = "SELECT * INTO OUTFILE '$backup_file' FROM $table_name";

        // $stmt = $this->conn->prepare("SELECT * INTO OUTFILE 'articles.sql' FROM articles");

        // if ($stmt->execute()) {
        //     return true;
        // }
        // list(,, $error) = $stmt->errorInfo();
        // Logging::error(LogError::ARTICLES_BACKUP, ['error' => $error]);
        // return false;
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
        Logging::error(LogError::ARTICLE_DELETE, ['error' => $this->conn->lastErrorMsg()]);
        return false;
    }

    /**
     *  Compose WHERE clause to be inserted into article database query; can be composed of several filters. SQLite LIKE statements don't use prepared statements and therefore aren't properly escaped. It doesn't matter as SQLITE is only for testing.
     * 
     * @param array $filters Array of ArtFilter Instances
     * @return string Where clause.
     */
    public  function printFilterStatement(array $filters): string
    {
        $str = 'WHERE ';
        $count = 0;

        Logging::debug('filters', $filters);

        //is key-value set and value not null or empty
        if (isset($filters[ArtFilter::NAME]) && $filters[ArtFilter::NAME]) {

            $str .= $this->use_sqlite ? "article_name LIKE '%{$filters[ArtFilter::NAME]}%'" : "article_name LIKE CONCAT ('%', :fname, '%')";
            $count += 1;
        }

        if (isset($filters[ArtFilter::LOCATION]) && $filters[ArtFilter::LOCATION]) {
            if ($count === 1) {
                $str .= ' AND ';
            }
            $str .= $this->use_sqlite ? "location LIKE '%{$filters[ArtFilter::LOCATION]}%'" : "location LIKE CONCAT ('%', :floc, '%')";
            $count += 1;
        }

        if (isset($filters[ArtFilter::DATE_BEFORE]) && $filters[ArtFilter::DATE_BEFORE]) {
            if ($count === 1) {
                $str .= ' AND ';
            }
            $str .= $this->use_sqlite ? "expiration_date < {$filters[ArtFilter::DATE_BEFORE]}" : "expiration_date < :fbefore";
            $count += 1;
        } else if (isset($filters[ArtFilter::DATE_AFTER]) && $filters[ArtFilter::DATE_AFTER]) {
            if ($count === 1) {
                $str .= ' AND ';
            }
            $str .= $this->use_sqlite ? "expiration_date > {$filters[ArtFilter::DATE_AFTER]}" : "expiration_date > :fafter";
            $count += 1;
        }

        if (!isset($filters[ArtFilter::SHOW_EXPIRED])) {

            Logging::debug('printFilterStatement');
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

        Logging::debug(
            'l',
            [
                'uid' => $uid,
                'name' =>  $name,
                'location' =>  $location,
                'date' => $date
            ]
        );

        $stmt->bindParam(':uid', $uid, $this->data_types['int']);
        $stmt->bindParam(':art', $name, $this->data_types['str']);
        $stmt->bindParam(':loc', $location, $this->data_types['str']);
        $stmt->bindParam(':date', $date, $this->data_types['str']);

        // $stmt->reset();

        $r = $stmt->execute();
        if ($r) {
            Logging::debug('execute');
        }
        // if ($r) {
        //     if ($this->use_sqlite) {
        //         $r->finalize(); //necessary to avoid double execution bug.
        //         // return $this->conn->lastInsertRowID();
        //     }
        //     // return intval($this->conn->lastInsertId());
        // }
        return 1;
        Logging::error(LogError::ARTICLE_INSERT, ['error' => $this->conn->lastErrorMsg()]);
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
            $row = $this->use_sqlite ? $r->fetchArray(SQLITE3_ASSOC) : $stmt->fetch();
            if ($row) {
                return Article::fromDatabaseRow($row);
            } else {
                Logging::error(LogError::ARTICLE_QUERY, ['id' => $id]);
            }
        } else {
            Logging::error(LogError::ARTICLE_QUERY, ['id' => $id, 'error' => $this->conn->lastErrorMsg()]);
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
        $filter_statement = $this->printFilterStatement($filters);
        $query = "SELECT COUNT(*) FROM articles $filter_statement";

        Logging::debug('queryCount stmt: ' . $query);
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
            if ($this->use_sqlite) {
                $array = $r->fetchArray();
                return $array['COUNT(*)'];
            }
            $r = $stmt->fetchColumn();
            return intval($r);
        }

        // list(,, $this->conn->lastErrorMsg()) = $stmt->errorInfo();
        Logging::error(LogError::ARTICLES_COUNT_QUERY, ['error' => $this->conn->lastErrorMsg()]);
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

        $articles = [];

        $r = $stmt->execute();
        if ($r) {
            // fetch next as associative array until there are none to be fetched.
            if ($this->use_sqlite) {
                while ($row = $r->fetchArray(SQLITE3_ASSOC)) {
                    array_push($articles, Article::fromDatabaseRow($row));
                }
            } else {
                while ($data = $stmt->fetch()) {
                    array_push($articles, Article::fromDatabaseRow($data));
                }
            }
        } else {
            Logging::error(LogError::ARTICLES_QUERY, ['error' => $this->conn->lastErrorMsg()]);
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
        Logging::error(LogError::ARTICLE_UPDATE, ['id' => $id, 'error' => $this->conn->lastErrorMsg()]);
        return false;
    }
}

    // /**
    //  * Delete all articles belonging to user.
    //  * 
    //  * @param int $user_id The user id.
    //  * @return bool True if the delete is successful.
    //  */
    // public function deleteUserArticles($user_id): bool
    // {
    //     $stmt = $this->conn->prepare('DELETE FROM articles WHERE user_id = :uid');
    //     $stmt->bindParam(':uid', $user_id, $this->data_types['int']);
    //     if ($stmt->execute()) {
    //         return true;
    //     }
    //     Logging::error(LogError::USER_ARTICLES_DELETE, ['error' => $this->conn->lastErrorMsg()]);
    //     return false;
    // }
