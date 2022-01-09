<?php

declare(strict_types=1);

################################
## Joël Piguet - 2022.01.09 ###
##############################

namespace app\helpers;

use app\constants\AppPaths;
use app\constants\Mode;
use app\constants\OrderBy;
use app\helpers\App;
use app\helpers\Logging;
use app\helpers\DBUtil;
use app\helpers\db\UserQueries;
use app\models\User;

use \PDO;
use SQLite3;

use PHPUnit\Framework\TestCase;

/**
 * Base class for testing classes containing hooks.
 */
class TestClass extends TestCase
{
    public static function getConn()
    {
        App::setMode(Mode::TESTS_SUITE);
        static $instance;
        if (!isset($instance)) {
            Logging::debug('instance is null');
            if (APP::useSQLite()) {
                $instance = DBUtil::localDBSetup(AppPaths::TEST_DB_FOLDER, 'local', true);
            } else {
                $instance = DBUtil::getMySQLConn();
            }
        }
        return $instance;
    }
}
