<?php

require_once __DIR__ . '/../boot.php';

use helpers\Database;

// $array = Database::getInstance()->getUserArticles(3, 10, 0);

// foreach ($array as $art) {
//     echo $art . PHP_EOL;
// }

echo Database::getInstance()->getUserById(1);
