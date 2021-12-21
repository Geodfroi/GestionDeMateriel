<?php

declare(strict_types=1);

################################
## Joël Piguet - 2021.12.21 ###
##############################

namespace app\helpers;

use DateTime;
use app\helpers\Logging;

/**
 * Utility class containing useful static functions.
 */
class Convert
{
    /**
     * Convert a string to datetime value.
     * 
     * @param string Str in format [Y-m-d H:i:s] or [Y-m-d].
     * @return DateTime DateTime value.
     */
    public static function toDateTime(string $str): DateTime
    {
        // Logging::debug('toDateTime', ['str' => $str]);
        $res = DateTime::createFromFormat('Y-m-d H:i:s', $str);
        if ($res) {
            return $res;
        }
        return DateTime::createFromFormat('Y-m-d', $str);
    }
}
