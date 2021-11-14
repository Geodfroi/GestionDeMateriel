<?php

require_once __DIR__ . '/../boot.php';

use helpers\Database;

$user = Database::getInstance()->getUserByEmail('aurore.azure@gmail.com');
echo $user . PHP_EOL;
