<?php

require_once __DIR__ . '/../vendor/autoload.php'; // use composer to load autofile.

use app\helpers\Database;
use app\models\User;
use app\helpers\Authenticate;
use app\helpers\UserOrder;
use app\helpers\Mailing;



// Mailing::passwordChangeNotification();

// foreach ($array as $art) {
// }

// for ($i = 0; $i < 20; $i++) {
//     $email = 'user_' . strval($i) . '@bogus.com';
//     $password = str_repeat(strval($i), 8);

//     $user = User::fromForm($email, $password, false);
//     // echo $user . PHP_EOL;
//     if (Database::getInstance()->insertUser($user)) {
//         echo 'inserted';
//     }
// }

// $users = Database::getInstance()->getUsers(50, 0, UserOrder::CREATED_ASC, false);
// foreach ($users as $value) {
//     echo $value . PHP_EOL;
// }
