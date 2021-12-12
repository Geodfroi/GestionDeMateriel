<?php

require_once __DIR__ . '/../vendor/autoload.php'; // use composer to load autofile.

use app\helpers\Logging;

Logging::app()->info('hi there.');
Logging::app()->info("wie get's?");

// print(Mailing::passwordChangeNotification('Johnny', ['aurore.azure@gmail.com'], '123123'));

// for ($i = 0; $i < 20; $i++) {
//     $email = 'user_' . strval($i) . '@bogus.com';
//     $password = str_repeat(strval($i), 8);

//     $user = User::fromForm($email, $password, false);
//     // echo $user . PHP_EOL;
//     if (Database::getInstance()->insertUser($user)) {
//         echo 'inserted';
//     }
// }
