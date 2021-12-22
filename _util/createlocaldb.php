<?php

declare(strict_types=1);

################################
## Joël Piguet - 2021.12.21 ###
##############################

use app\constants\AppPaths;
use app\constants\LogChannel;
use app\constants\Mode;
use app\helpers\App;
use app\helpers\TestUtil;

require_once __DIR__ . '/../vendor/autoload.php'; // use composer to load autofile.
App::setMode(Mode::WEB_APP);

$local_path = AppPaths::TEST_DB_FOLDER . DIRECTORY_SEPARATOR  . 'localDB.db';
$conn = TestUtil::localDBSetup($local_path);
