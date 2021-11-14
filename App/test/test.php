<?php

require_once __DIR__ . '/../boot.php';

use helpers\Database;

// $user = Database::getInstance()->getUserByEmail('aurore.azure@gmail.com');
// echo $user;

echo Database::getInstance()->getUserById(2);

// use helpers\Mailing;

// Mailing::passwordChangeNotification("aurore.azure@gmail.com", 'aaaaaa');
