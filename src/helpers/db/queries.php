<?php

declare(strict_types=1);

################################
## Joël Piguet - 2022.03.10 ###
##############################

namespace app\helpers\db;
// use app\helpers\Logging;

use \PDO;
use SQLite3;

/**
 * Base class for db queries; handle differences between MySQL and SQLite queries
 */
class Queries
{
    protected $conn;

    /**
     * array
     */
    protected $data_types;

    /**
     * @param PDO|SQlite3 $conn Db connection.
     */
    function __construct($conn)
    {
        $this->conn = $conn;
        $this->data_types = [
            'int' => USE_SQLITE ? SQLITE3_INTEGER : PDO::PARAM_INT,
            'str' => USE_SQLITE ? SQLITE3_TEXT : PDO::PARAM_STR,
            'bool' => USE_SQLITE ? SQLITE3_INTEGER : PDO::PARAM_BOOL,
        ];
    }

    /**
     * @param SQLite3Result|bool $r Result.
     * @param $stmt Prepared statement.
     */
    protected function count($r, $stmt): int
    {
        if (USE_SQLITE) {
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
        if (USE_SQLITE) {
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
        return USE_SQLITE ? $r->fetchArray(SQLITE3_ASSOC) : $stmt->fetch(PDO::FETCH_ASSOC);
    }

    protected function rowId(): int
    {
        return USE_SQLITE ? $this->conn->lastInsertRowID() : intval($this->conn->lastInsertId());
    }
}
