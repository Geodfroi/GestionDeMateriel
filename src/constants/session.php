<?php

declare(strict_types=1);

################################
## Joël Piguet - 2021.12.07 ###
##############################

namespace app\constants;

/**
 * SESSION global variables keys.
 */
class Session
{
    const USERS_ORDERBY = 'admin_orderby';
    const USERS_PAGE = 'admin_page';
    const ART_ORDERBY = 'articles_orderby';
    const ART_PAGE = 'articles_page';
    const ART_FILTER_TYPE = 'article_filter_type';
    const ART_FILTER_VAL = 'article_filter_value';

    const ADMIN_ID = 'admin_id';
    const USER_ID = 'user_id';
}
