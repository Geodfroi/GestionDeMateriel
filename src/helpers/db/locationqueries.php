<?php

declare(strict_types=1);

################################
## Joël Piguet - 2021.12.21 ###
##############################

namespace app\helpers\db;

use \PDO;
use app\constants\LogError;
use app\helpers\Database;
use app\helpers\Logging;
use app\models\StringContent;

use function PHPUnit\Framework\isNull;

/**
 * Regroup function to interact with locations table.
 */
class LocationQueries
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
        Logging::debug('location debug not implemented');
    }

    /**
     * Delete location from db by id.
     * 
     * @param int $id Location id.
     * @return bool True if the delete is successful.
     */
    public function delete(int $id): bool
    {
        $stmt = $this->conn->prepare('DELETE FROM locations WHERE id = :id');
        $stmt->bindParam(':id', $id, $this->data_types['int']);

        if ($stmt->execute()) {
            return true;
        }
        //list(,, $error) = $stmt->errorInfo();
        Logging::error(LogError::LOCATION_DELETE, ['error' => $this->conn->lastErrorMsg()]);
        return false;
    }

    /**     
     * Insert a loaction string into the database.
     * 
     * @param string $str New location string.
     * @return int ID of inserted row or 0 if it fails.
     */
    public function insert(?string $str): int
    {
        if (!$str) {
            Logging::debug('insert shortened');
            return 0;
        }

        $stmt = $this->conn->prepare('INSERT INTO locations (str_content) VALUES (:str)');
        $stmt->bindParam(':str', $str, $this->data_types['str']);

        $r = $stmt->execute();
        if ($r) {
            return $this->use_sqlite ? $this->conn->lastInsertRowID() : intval($this->conn->lastInsertId());
        }

        Logging::error(LogError::LOCATION_INSERT, ['error' => $this->conn->lastErrorMsg()]);
        return 0;
    }

    /**
     * Retrieve a single Location by id.
     * 
     * @param int $id Item id.
     * @return StringContent|null
     */
    public function queryById(int $id): ?StringContent
    {
        $stmt = $this->conn->prepare('SELECT 
            id, 
            str_content 
        FROM locations WHERE id = :id');

        $stmt->bindParam(':id', $id, $this->data_types['int']);

        $r = $stmt->execute();
        if ($r) {
            // retrieve only first row found; fine since id is unique.
            $row = $this->use_sqlite ? $r->fetchArray(SQLITE3_ASSOC) : $stmt->fetch();
            if ($row) {
                return StringContent::fromDatabaseRow($row);
            }
            Logging::error(LogError::LOCATION_QUERY, ['id' => $id]);
            return null;
        }
        Logging::error(LogError::LOCATION_QUERY, ['id' => $id, 'error' =>  $this->conn->lastErrorMsg()]);
        return null;
    }

    /**
     * Retrieve user array from database.
     * 
     * @return array Array of locations.
     */
    public function queryAll(): array
    {
        $stmt = $this->conn->prepare('SELECT id, str_content FROM locations ORDER BY str_content ASC');

        $r = $stmt->execute();
        if ($r) {
            $locations = [];

            if ($this->use_sqlite) {
                while ($row = $r->fetchArray(SQLITE3_ASSOC)) {
                    array_push($locations, StringContent::fromDatabaseRow($row));
                }
            } else {
                // fetch next as associative array until there are none to be fetched.
                while ($row = $stmt->fetch()) {
                    array_push($locations, StringContent::fromDatabaseRow($row));
                }
            }
            return $locations;
        }

        Logging::error(LogError::LOCATIONS_QUERY_ALL, ['error' => $this->conn->lastErrorMsg()]);
        return [];
    }

    /**
     * Check if content already exists in database
     * @param string $content Location content.
     * @return bool True if already present.
     */
    public function contentExists(string $content): bool
    {
        $stmt = $this->conn->prepare('SELECT 
            COUNT(*)
            FROM locations
            WHERE str_content = :str');

        $stmt->bindParam(':str', $content, $this->data_types['str']);
        $r = $stmt->execute();
        if ($r) {
            if ($this->use_sqlite) {
                return $r->numColumns() === 1;
            }
            $c = $stmt->fetchColumn();
            return intval($c) === 1;
        }

        Logging::error(LogError::LOCATIONS_CHECK_CONTENT, ['error' => $this->conn->lastErrorMsg()]);
        return false;
    }

    /**
     * Update user alias.
     * 
     * @param int $location_id Location object id.
     * @param string $str New location string.
     * @return bool True is update is successful.
     */
    public function update(int $location_id, string $str)
    {
        $stmt = $this->conn->prepare('UPDATE locations SET str_content=:str WHERE id = :id');

        $stmt->bindParam(':id', $location_id, $this->data_types['int']);
        $stmt->bindParam(':str', $str, $this->data_types['str']);

        if ($stmt->execute()) {
            return true;
        }
        Logging::error(LogError::LOCATION_UPDATE, ['error' => $this->conn->lastErrorMsg()]);
        return false;
    }
}
