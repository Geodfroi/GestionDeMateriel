<?php

declare(strict_types=1);

################################
## Joël Piguet - 2021.12.12 ###
##############################

namespace app\constants;

/**
 * Log info messages.
 */
class LogInfo
{
    const ARTICLE_CREATED = 'New article successfully created.';
    const ARTICLE_DELETED = 'Article successfully deleted.';
    const ARTICLE_UPDATED = 'Article successfully updated.';

    const LOCATION_CREATED = 'New location successfully added.';
    const LOCATION_DELETED = 'Existing location successfully deleted.';
    const LOCATION_UPDATED = 'Location was successfully updated.';

    const NEW_PASSWORD_ISSUED = 'New password successfully sent.';

    const ROUTING = 'Routing...';

    const USER_LOGIN = 'User login';
    const USER_LOGOUT = 'User logout';

    const USER_CREATED = 'New user successfully created.';
    const USER_DELETED = 'Article successfully deleted.';
    const USER_UPDATED = 'Article successfully updated.';
}
