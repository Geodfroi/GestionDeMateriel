<?php

require_once __DIR__ . '/../boot.php';

use helpers\Database;
use models\User;
use helpers\Authenticate;
use helpers\UserOrder;

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

$users = Database::getInstance()->getUsers(50, 0, UserOrder::CREATED_ASC, false);
foreach ($users as $value) {
    echo $value . PHP_EOL;
}
