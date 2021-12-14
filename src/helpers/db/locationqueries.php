<?php

declare(strict_types=1);

################################
## Joël Piguet - 2021.12.12 ###
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
    private PDO $pdo;
    private int $logger;

    function __construct(PDO $pdo, int $logger)
    {
        $this->pdo = $pdo;
        $this->logger = $logger;
    }

    /**
     * Delete location from db by id.
     * 
     * @param int $id Location id.
     * @return bool True if the delete is successful.
     */
    public function delete(int $id): bool
    {
        $preparedStatement = $this->pdo->prepare('DELETE FROM locations WHERE id = :id');
        $preparedStatement->bindParam(':id', $id, PDO::PARAM_INT);

        if ($preparedStatement->execute()) {
            return true;
        }
        list(,, $error) = $preparedStatement->errorInfo();
        Logging::error(LogError::LOCATION_DELETE, ['error' => $error], $this->logger);
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
        $preparedStatement = $this->pdo->prepare('INSERT INTO locations (str_content) VALUES (:str)');
        $preparedStatement->bindParam(':str', $str, PDO::PARAM_STR);

        if ($preparedStatement->execute()) {
            return intval($this->pdo->lastInsertId());
        }
        list(,, $error) = $preparedStatement->errorInfo();
        Logging::error(LogError::LOCATION_INSERT, ['error' => $error], $this->logger);
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
        $preparedStatement = $this->pdo->prepare('SELECT 
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
            list(,, $error) = $preparedStatement->errorInfo();
            Logging::error(LogError::LOCATION_QUERY, ['id' => $id, 'error' =>  $error], $this->logger);
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
        $preparedStatement = $this->pdo->prepare('SELECT id, str_content FROM locations ORDER BY str_content ASC');

        $locations = [];

        if ($preparedStatement->execute()) {
            // fetch next as associative array until there are none to be fetched.
            while ($data = $preparedStatement->fetch()) {
                array_push($locations, StringContent::fromDatabaseRow($data));
            }
        } else {
            list(,, $error) = $preparedStatement->errorInfo();
            Logging::error(LogError::LOCATIONS_QUERY_ALL, ['error' => $error], $this->logger);
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
        $preparedStatement = $this->pdo->prepare('SELECT 
            COUNT(*)
            FROM locations
            WHERE str_content = :str');

        $preparedStatement->bindParam(':str', $content, PDO::PARAM_STR);
        if ($preparedStatement->execute()) {
            $r = $preparedStatement->fetchColumn();
            return intval($r) === 1;
        }
        list(,, $error) = $preparedStatement->errorInfo();
        Logging::error(LogError::LOCATIONS_CHECK_CONTENT, ['error' => $error], $this->logger);
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
        $preparedStatement = $this->pdo->prepare('UPDATE locations SET str_content=:str WHERE id = :id');

        $preparedStatement->bindParam(':id', $location_id, PDO::PARAM_INT);
        $preparedStatement->bindParam(':str', $str, PDO::PARAM_STR);

        if ($preparedStatement->execute()) {
            return true;
        }
        list(,, $error) = $preparedStatement->errorInfo();
        Logging::error(LogError::LOCATION_UPDATE, ['error' => $error], $this->logger);
        return false;
    }
}
