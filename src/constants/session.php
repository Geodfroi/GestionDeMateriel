<?php

declare(strict_types=1);

################################
## Joël Piguet - 2022.03.14 ###
##############################

namespace app\constants;

/**
 * SESSION global variables keys.
 */
class Session
{
    const ARTICLES_DISPLAY = 'articles_display_json';
    const USERS_DISPLAY = 'users_display_json';

    const USER_ID = 'user_id';
    const IS_ADMIN = 'is_admin';

    const ALERT = 'alert';
    const PAGE = 'page';
    const PAGE_URL = 'page_url';
    const ROOT = 'root_url';
}
