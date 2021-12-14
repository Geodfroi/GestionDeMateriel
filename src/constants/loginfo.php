<?php

declare(strict_types=1);

################################
## Joël Piguet - 2021.12.14 ###
##############################

namespace app\constants;

/**
 * Log info messages.
 */
class LogInfo
{
    const ARTICLE_CREATED = 'uew article successfully created.';
    const ARTICLE_DELETED = 'article successfully deleted.';
    const ARTICLE_UPDATED = 'article successfully updated.';

    const LOCATION_CREATED = 'new location successfully added.';
    const LOCATION_DELETED = 'existing location successfully deleted.';
    const LOCATION_UPDATED = 'location was successfully updated.';

    const NEW_PASSWORD_ISSUED = 'new password successfully sent.';

    const ROUTING = 'routing...';

    const USER_LOGIN = 'user login';
    const USER_LOGOUT = 'user logout';

    const USER_CREATED = 'new user successfully created.';
    const USER_DELETED = 'user successfully deleted.';
    const USER_UPDATED = 'user successfully updated.';
}
