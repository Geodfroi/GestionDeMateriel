<?php

declare(strict_types=1);

################################
## Joël Piguet - 2021.12.20 ###
##############################

namespace app\helpers;

use PHPUnit\Framework\TestCase;
use app\constants\Globals;
use app\constants\LogChannel;

/**
 * Base class for testing classes containing hooks.
 */
class TestClass extends TestCase
{
    // /**
    //  * @beforeClass
    //  */
    // public static function beforeAll(): void
    // {
    //     // Logging::error('beforeall');
    //     $GLOBALS[Globals::LOG_CHANNEL] = LogChannel::TEST;
    //     $GLOBALS[Globals::IS_TEST] = true;
    // }
}
