<?php

declare(strict_types=1);

################################
## Joël Piguet - 2021.12.20 ###
##############################

namespace app\constants;

class Globals
{
    const LOG_CHANNEL = 'log-channel';
    const DATABASE = 'db';

    const USE_MYSQL = 0;
    /**
     * Use local sqlite db instead of mySQL for debug..
     */
    const USE_DEBUG_LOCAL = 1;
    /**
     * Use temporary test sqlite db instead.
     */
    const USE_UNIT_TEST = 2;
}
