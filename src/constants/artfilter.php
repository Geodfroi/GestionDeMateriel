<?php

declare(strict_types=1);

################################
## Joël Piguet - 2021.12.13 ###
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
    const DATE_BEFORE = 'before-peremption';
    const DATE_AFTER = 'after-peremption';
    const SHOW_EXPIRED = 'show-expired';
}
