<?php

require_once __DIR__ . '/../boot.php';

use helpers\Database;

$user = Database::getInstance()->getUserByEMail('aurore.azure@gmail.com');
echo $user;
