<?php

declare(strict_types=1);

################################
## Joël Piguet - 2022.01.18 ###
##############################

namespace app\constants;

/**
 * Filters for article table queries.
 */
class ArtFilter
{
    // //db article filters
    const NAME = 'name';
    const LOCATION = 'location';
    const DATE_BEFORE = 'before_peremption';
    const DATE_AFTER = 'after_peremption';
    const SHOW_EXPIRED = 'show_expired';
}
