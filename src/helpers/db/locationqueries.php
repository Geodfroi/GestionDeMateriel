<?php

declare(strict_types=1);

################################
## Joël Piguet - 2021.12.07 ###
##############################

namespace app\helpers\db;

use \PDO;

use app\constants\Error;
use app\models\StringContent;

/**
 * Regroup function to interact with locations table.
 */
class LocationQueries
{
    private PDO $pdo;

    function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
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
        error_log(Error::LOCATION_DELETE . $error . PHP_EOL);
        return false;
    }

    /**     
     * Insert a loaction string into the database.
     * 
     * @param string $str New location string.
     * @return bool True if the insert is successful.
     */
    public function insert(string $str): bool
    {
        $preparedStatement = $this->pdo->prepare('INSERT INTO locations (str_content) VALUES (:str)');
        $preparedStatement->bindParam(':str', $str, PDO::PARAM_STR);

        if ($preparedStatement->execute()) {
            return true;
        }
        list(,, $error) = $preparedStatement->errorInfo();
        error_log(Error::LOCATION_INSERT . $error . PHP_EOL);
        return false;
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
                error_log(sprintf(Error::LOCATION_QUERY, $id));
            }
        } else {
            list(,, $error) = $preparedStatement->errorInfo();
            error_log(sprintf(Error::LOCATION_QUERY, $id) . $error . PHP_EOL);
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
            error_log(Error::LOCATIONS_QUERY_ALL  . $error . PHP_EOL);
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
        error_log(Error::LOCATIONS_CHECK_CONTENT . $error . PHP_EOL);
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
        error_log(Error::LOCATION_UPDATE . $error . PHP_EOL);
        return false;
    }
}
