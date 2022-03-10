<?php

declare(strict_types=1);

################################
## Joël Piguet - 2022.03.10 ###
##############################

namespace app\helpers;

use app\constants\AppPaths;
use app\helpers\Logging;
use app\helpers\DBUtil;
use PHPUnit\Framework\TestCase;

/**
 * Base class for testing classes containing hooks.
 */
class TestClass extends TestCase
{
    public static function getConn()
    {
        static $instance;
        if (!isset($instance)) {
            Logging::debug('instance is null');
            if (USE_SQLITE) {
                $instance = DBUtil::localDBSetup(AppPaths::TEST_DB_FOLDER . DIRECTORY_SEPARATOR . 'local.db', true);
            } else {
                $instance = DBUtil::getMySQLConn();
            }
        }
        return $instance;
    }
}
