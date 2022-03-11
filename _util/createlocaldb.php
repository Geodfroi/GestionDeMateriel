<?php

declare(strict_types=1);

################################
## Joël Piguet - 2022.03.10 ###
##############################

use app\constants\AppPaths;
use app\helpers\DBUtil;

require_once __DIR__ . '/../vendor/autoload.php'; // use composer to load autofile.

const DEBUG_MODE = true;
const LOG_CHANNEL = "create-db";
$conn = DBUtil::localDBSetup(AppPaths::LOCAL_DB_FOLDER . DIRECTORY_SEPARATOR . 'localDB.db', true);
