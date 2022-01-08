<?php

declare(strict_types=1);

################################
## Joël Piguet - 2022.01.08 ###
##############################

namespace app\helpers\db;

use Exception;
use SQLite3;

use app\constants\LogError;
use app\helpers\App;
use app\helpers\Logging;
use app\models\StringContent;

/**
 * Regroup function to interact with locations table.
 */
class LocationQueries extends Queries
{
    /**
     * @param SQlite3 $backup_conn Db backup connection.
     * @return True if backup is successful.
     */
    public function backup(SQlite3 $backup_conn): bool
    {
        $stmt = $this->conn->prepare("SELECT * FROM locations");
        $r = $stmt->execute();
        while ($row = $this->fetchRow($r, $stmt)) {
            $id  = (int)($row['id'] ?? 0);
            $str_content = (string)($row['str_content'] ?? '');

            $stmt = $backup_conn->prepare('INSERT INTO locations (id, str_content) VALUES (:id, :str)');

            $stmt->bindParam(':id', $id, SQLITE3_INTEGER);
            $stmt->bindParam(':str', $str_content, SQLITE3_TEXT);

            if (!$stmt->execute()) {
                Logging::error('failure to insert location in backup db', ['location' => $str_content]);
                return false;
            };
        }

        return true;
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
        Logging::error(LogError::LOCATION_DELETE, ['error' => $this->error($stmt)]);
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
            return 0;
        }

        $stmt = $this->conn->prepare('INSERT INTO locations (str_content) VALUES (:str)');
        $stmt->bindParam(':str', $str, $this->data_types['str']);

        $r = $stmt->execute();
        if ($r) {
            return $this->rowId();
        }

        Logging::error(LogError::LOCATION_INSERT, ['error' => $this->error($stmt)]);
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
            $row = $this->fetchRow($r, $stmt);
            if ($row) {
                return StringContent::fromDatabaseRow($row);
            }
            Logging::error(LogError::LOCATION_QUERY, ['id' => $id]);
            return null;
        }
        Logging::error(LogError::LOCATION_QUERY, ['id' => $id, 'error' =>  $this->error($stmt)]);
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
            // fetch next as associative array until there are none to be fetched.
            while ($row = $this->fetchRow($r, $stmt)) {
                array_push($locations, StringContent::fromDatabaseRow($row));
            }
            return $locations;
        }

        Logging::error(LogError::LOCATIONS_QUERY_ALL, ['error' => $this->error($stmt)]);
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
            if (App::useSQLite()) {
                return $r->numColumns() === 1;
            }
            $c = $stmt->fetchColumn();
            return intval($c) === 1;
        }

        Logging::error(LogError::LOCATIONS_CHECK_CONTENT, ['error' => $this->error($stmt)]);
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
        Logging::error(LogError::LOCATION_UPDATE, ['error' => $this->error($stmt)]);
        return false;
    }
}
