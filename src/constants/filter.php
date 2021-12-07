<?php

declare(strict_types=1);

################################
## Joël Piguet - 2021.12.07 ###
##############################

namespace app\constants;

/**
 * Filters for article table queries.
 */
class Filter
{
    //db article filters
    const ARTICLE_NAME = 0;
    const LOCATION = 1;
    const DATE = 2;

    /**
     * Get label from Filter const value.
     * 
     * @param int $const Filter constant value.
     * @return string bal
     */
    public static function getLabel(int $const): string
    {
        if ($const === Filter::LOCATION) {
            return 'Par emplacement';
        } else if ($const === Filter::DATE) {
            return 'Par date de péremption';
        }
        return "Par nom d'article";
    }
}
