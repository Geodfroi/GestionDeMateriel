<?php

declare(strict_types=1);

################################
## Joël Piguet - 2021.12.15 ###
##############################

namespace app\helpers\db;

use \PDO;
use app\constants\LogError;
use app\helpers\Logging;
use app\models\StringContent;

/**
 * Regroup function to interact with locations table.
 */
class LocationQueries
{
    /**
     * @param PDO|SQlite3 $conn Db connection.
     * @param int $logger Logger channel.
     */
    function __construct($conn, string $logger)
    {
        $this->conn = $conn;
        $this->logger = $logger;
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
        $preparedStatement = $this->conn->prepare('DELETE FROM locations WHERE id = :id');
        $preparedStatement->bindParam(':id', $id, PDO::PARAM_INT);

        if ($preparedStatement->execute()) {
            return true;
        }
        //list(,, $error) = $preparedStatement->errorInfo();
        Logging::error(LogError::LOCATION_DELETE, ['error' => $this->conn->lastErrorMsg()], $this->logger);
        return false;
    }

    /**     
     * Insert a loaction string into the database.
     * 
     * @param string $str New location string.
     * @return int ID of inserted row or 0 if it fails.
     */
    public function insert(string $str): int
    {
        $preparedStatement = $this->conn->prepare('INSERT INTO locations (str_content) VALUES (:str)');
        $preparedStatement->bindParam(':str', $str, PDO::PARAM_STR);

        if ($preparedStatement->execute()) {
            return intval($this->conn->lastInsertId());
        }
        Logging::error(LogError::LOCATION_INSERT, ['error' => $this->conn->lastErrorMsg()], $this->logger);
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
        $preparedStatement = $this->conn->prepare('SELECT 
            id, 
            str_content 
        FROM locations WHERE id = :id');

        $preparedStatement->bindParam(':id', $id, PDO::PARAM_INT);

        if ($preparedStatement->execute()) {

            $data = $preparedStatement->fetch(); // retrieve only first row found; fine since id is unique.
            if ($data) {
                return StringContent::fromDatabaseRow($data);
            } else {
                Logging::error(LogError::LOCATION_QUERY, ['id' => $id], $this->logger);
            }
        } else {
            Logging::error(LogError::LOCATION_QUERY, ['id' => $id, 'error' =>  $this->conn->lastErrorMsg()], $this->logger);
        }

        return null;
    }

    /**
     * Retrieve user array from database.
     * 
     * @return array Array of locations.
     */
    public function queryAll(): array
    {
        $preparedStatement = $this->conn->prepare('SELECT id, str_content FROM locations ORDER BY str_content ASC');

        $locations = [];

        if ($preparedStatement->execute()) {
            // fetch next as associative array until there are none to be fetched.
            while ($data = $preparedStatement->fetch()) {
                array_push($locations, StringContent::fromDatabaseRow($data));
            }
        } else {
            Logging::error(LogError::LOCATIONS_QUERY_ALL, ['error' => $this->conn->lastErrorMsg()], $this->logger);
        }

        return $locations;
    }

    /**
     * Check if content already exists in database
     * @param string $content Location content.
     * @return bool True if already present.
     */
    public function contentExists(string $content): bool
    {
        $preparedStatement = $this->conn->prepare('SELECT 
            COUNT(*)
            FROM locations
            WHERE str_content = :str');

        $preparedStatement->bindParam(':str', $content, PDO::PARAM_STR);
        if ($preparedStatement->execute()) {
            $r = $preparedStatement->fetchColumn();
            return intval($r) === 1;
        }

        Logging::error(LogError::LOCATIONS_CHECK_CONTENT, ['error' => $this->conn->lastErrorMsg()], $this->logger);
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
        $preparedStatement = $this->conn->prepare('UPDATE locations SET str_content=:str WHERE id = :id');

        $preparedStatement->bindParam(':id', $location_id, PDO::PARAM_INT);
        $preparedStatement->bindParam(':str', $str, PDO::PARAM_STR);

        if ($preparedStatement->execute()) {
            return true;
        }
        Logging::error(LogError::LOCATION_UPDATE, ['error' => $this->conn->lastErrorMsg()], $this->logger);
        return false;
    }
}
