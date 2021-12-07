<?php

declare(strict_types=1);

################################
## Joël Piguet - 2021.12.07 ###
##############################

namespace app\constants;

class OrderBy
{
    const CREATED_ASC = 0;
    const CREATED_DESC = 1;
    const DATE_ASC = 2;
    const DATE_DESC = 3;
    const EMAIL_ASC = 4;
    const EMAIL_DESC = 5;
    const LOCATION_ASC = 6;
    const LOCATION_DESC = 7;
    const NAME_ASC = 8;
    const NAME_DESC = 9;
    const LOGIN_ASC = 10;
    const LOGIN_DESC = 11;
    const OWNED_BY = 12;

    /**
     * Return orderby query element.
     * 
     * @param int $const OrderBy Const value.
     * @return Array orderby string parameters.
     */
    public static function getOrderParameters(int $const): array
    {
        switch ($const) {

            case OrderBy::CREATED_ASC:
                return ['creation_date', 'ASC'];
            case OrderBy::CREATED_DESC:
                return ['creation_date', 'DESC'];
            case OrderBy::DATE_ASC:
                return ['expiration_date', 'ASC'];
            case OrderBy::DATE_DESC:
                return ['expiration_date', 'DESC'];
            case OrderBy::EMAIL_ASC:
                return ['email', 'ASC'];
            case OrderBy::EMAIL_DESC:
                return ['email', 'DESC'];
            case OrderBy::LOCATION_ASC:
                return ['location', 'ASC'];
            case OrderBy::LOCATION_DESC:
                return ['location', 'DESC'];
            case OrderBy::LOGIN_ASC:
                return ['last_login', 'ASC'];
            case OrderBy::LOGIN_DESC:
                return ['last_login', 'DESC'];
            case OrderBy::NAME_ASC:
                return ['article_name', 'ASC'];
            case OrderBy::NAME_DESC:
                return ['article_name', 'DESC'];
            case OrderBy::OWNED_BY:
                return ['user_id', 'ASC'];
            default:
                return [];
        }
    }
}
