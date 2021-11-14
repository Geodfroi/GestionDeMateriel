<?php

declare(strict_types=1);

################################
## Joël Piguet - 2021.11.14 ###
##############################

namespace helpers;

use \PDO;
use models\User;
use PDOException;

/**
 * Database class accessible throughout the application by calling getInstance() method. 
 */
class Database
{
    private $pdo;

    /**
     * Initialise connection to the MySQL inside the constructor dunder method.
     * ! put the connection parameters (including admin password to db) in a settings.php file which is not shared on github.
     */
    function __construct()
    {
        try {
            $dsn = 'mysql:host=' . HOST . ';port=' . PORT . ';dbname=' . DB_NAME . ';charset=utf8mb4';
            echo $dsn;
            $options = [
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ];
            $this->pdo = new PDO($dsn, ADMIN_ID, ADMIN_PASSWORD, $options);
        } catch (PDOException $e) {
            echo 'Connection failed: ' . $e->getMessage();
        }
    }

    /**
     * Simpleton pattern insures there is only one instance of Database class in the whole application
     */
    public static function getInstance()
    {
        static $instance;
        if (is_null($instance)) {
            $instance = new static();
        }
        return $instance;
    }

    public function getUserByEMail(string $email): ?User
    {
        // $stmt = $this->pdo->prepare("SELECT * FROM users");
        // if ($stmt->execute()) {
        //     echo "1";
        //     // echo $data;
        //     echo $stmt->columnCount();
        //     return null;
        //     // return new User($data);
        // }
        // return null;

        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE email = :email");
        if ($stmt->execute([':email' => $email]) && ($data = $stmt->fetch(PDO::FETCH_ASSOC))) {
            echo '1';
            echo $data;
            return new User($data);
        }
        return null;


        // // echo $this->pdo;
        // echo $email;
        // $preparedStatement = $this->pdo->prepare('SELECT 
        //     id, 
        //     email, 
        //     password, 
        //     creation_date, 
        //     is_admin 
        // FROM users WHERE email = :email');

        // $preparedStatement->bindParam(':email', $email);
        // $preparedStatement->execute();



        // // if ($preparedStatement->execute()) {
        // // echo 'executed';
        // # return result as associative array;

        // $data = $preparedStatement->fetch(PDO::FETCH_ASSOC);
        // echo '1';
        // echo $data;
        // if ($data) {
        //     return new User($data);
        // }
        // // } else {
        // //     echo 'not executed';
        // // }
        // return null;
    }
}
