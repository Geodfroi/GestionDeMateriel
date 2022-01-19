<?php

declare(strict_types=1);

################################
## Joël Piguet - 2022.01.18 ###
##############################

namespace app\constants;

/**
 * SESSION global variables keys.
 */
class Session
{
    const USERS_ORDERBY = 'admin_orderby';
    const USERS_PAGE = 'admin_page';

    const ARTICLE_DISPLAY = 'article_display_json';

    // /**
    //  * store associative array with key as filter type and value as filter params.
    //  */
    // const ART_DISPLAY_COUNT = 'article_display_count';
    // const ART_FILTERS = 'article_filters';
    // const ART_ORDERBY = 'articles_orderby';
    // const ART_PAGE = 'articles_page';

    const USER_ID = 'user_id';
    const IS_ADMIN = 'is_admin';

    const ALERT = 'alert';
}
