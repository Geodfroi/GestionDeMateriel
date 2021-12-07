<?php

declare(strict_types=1);

################################
## Joël Piguet - 2021.12.07 ###
##############################

namespace app\constants;

use Exception;

/**
 * Filters for article table queries.
 */
class Filter
{
    //db article filters
    const ARTICLE_NAME = 0;
    const LOCATION = 1;
    const DATE_BEFORE = 2;
    const DATE_AFTER = 3;

    /**
     * Get label from Filter const value.
     * 
     * @param int $filter_type Filter constant value.
     * @return string bal
     */
    public static function getLabel(int $filter_type): string
    {
        switch ($filter_type) {
            case Filter::LOCATION:
                return 'Par emplacement:';
            case Filter::DATE_BEFORE:
                return 'Avant la date suivante:';
            case Filter::DATE_AFTER:
                return 'Après la date suivante:';
            case Filter::ARTICLE_NAME:
            default:
                return "Par nom d'article:";
        }
    }

    /**
     * Compose WHERE clause to be inserted into article database query.
     */
    public static function printStatement($filter_type): string
    {
        switch ($filter_type) {
            case Filter::ARTICLE_NAME:
                return "WHERE article_name LIKE CONCAT('%', :fil, '%')";
            case Filter::LOCATION:
                return "WHERE location LIKE CONCAT('%', :fil, '%')";
            case Filter::DATE_BEFORE:
                return "WHERE expiration_date < :fil";
            case Filter::DATE_AFTER:
                return "WHERE expiration_date > :fil";
            default:
                break;
        }
        throw new Exception('printStatement:: Invalid arguments');
    }
}
