<?php

declare(strict_types=1);

################################
## Joël Piguet - 2022.01.19 ###
##############################

use app\constants\AppPaths;
use app\constants\Mode;
use app\helpers\App;
use app\helpers\DBUtil;

require_once __DIR__ . '/../vendor/autoload.php'; // use composer to load autofile.
App::setMode(Mode::WEB_APP);

$conn = DBUtil::localDBSetup(AppPaths::LOCAL_DB_FOLDER . DIRECTORY_SEPARATOR . 'localDB.db', true);
