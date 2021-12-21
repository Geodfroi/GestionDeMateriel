<?php

declare(strict_types=1);

################################
## Joël Piguet - 2021.12.21 ###
##############################

use app\constants\Globals;
use app\constants\AppPaths;
use app\constants\LogChannel;
use app\helpers\TestUtil;

require_once __DIR__ . '/../vendor/autoload.php'; // use composer to load autofile.
$GLOBALS[Globals::LOG_CHANNEL] = LogChannel::TEST;

$local_path = AppPaths::TEST_DB_FOLDER . DIRECTORY_SEPARATOR  . 'localDB.db';
$conn = TestUtil::localDBSetup($local_path);
