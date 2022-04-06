<?php

declare(strict_types=1);

################################
## Joël Piguet - 2022.04.06 ###
##############################

namespace app\constants;

/**
 * Filters for article table queries.
 */
class ArtFilter
{
    //db article filters
    const AUTHOR = 'author';
    const NAME = 'name';
    const LOCATION = 'location';
    const DATE_VALUE = 'peremption_value';
    const DATE_TYPE = 'peremption_type';
    const SHOW_EXPIRED = 'show_expired';
    const DATE_BEFORE = 'before';
    const DATE_AFTER = 'after';

    // filter defaults
    const EVERYONE = 'tous le monde';
}
