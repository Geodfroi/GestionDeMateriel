<?php

declare(strict_types=1);

################################
## Joël Piguet - 2021.12.22 ###
##############################

namespace app\helpers\db;

use \PDO;
use SQLite3;

use app\helpers\App;
use app\helpers\Logging;

/**
 * Base class for db queries; handle differences between MySQL and SQLite queries
 */
class Queries
{
    protected $conn;
    protected array $data_types;

    /**
     * @param PDO|SQlite3 $conn Db connection.
     */
    function __construct($conn)
    {
        $this->conn = $conn;
        $this->data_types = [
            'int' => APP::useSQLite() ? SQLITE3_INTEGER : PDO::PARAM_INT,
            'str' => APP::useSQLite() ? SQLITE3_TEXT : PDO::PARAM_STR,
            'bool' => APP::useSQLite() ? SQLITE3_INTEGER : PDO::PARAM_BOOL,
        ];
    }

    /**
     * @param SQLite3Result|bool $r Result.
     * @param $stmt Prepared statement.
     */
    protected function count($r, $stmt): int
    {
        if (App::useSQLite()) {
            $array = $r->fetchArray();
            return $array['COUNT(*)'];
        }
        $c = $stmt->fetchColumn();
        return intval($c);
    }

    /**
     * @param $stmt Prepared statement.
     */
    protected function error($stmt): string
    {
        if (App::useSQLite()) {
            $this->conn->lastErrorMsg();
        }
        list(,, $error) = $stmt->errorInfo();
        return $error;
    }
    /**
     * @param SQLite3Result|bool $r Result.
     * @param $stmt Prepared statement.
     * @return array|bool Return row as associative array of false if no more is to be found.
     */
    protected function fetchRow($r, $stmt)
    {
        return App::useSQLite() ? $r->fetchArray(SQLITE3_ASSOC) : $stmt->fetch(PDO::FETCH_ASSOC);
    }

    protected function rowId(): int
    {
        return App::useSQLite() ? $this->conn->lastInsertRowID() : intval($this->conn->lastInsertId());
    }
}
