<?php

declare(strict_types=1);

################################
## Joël Piguet - 2021.12.15 ###
##############################

namespace app\constants;

/**
 * Private setting not to be stored in github. Fill-in and remove '_template suffix.
 */
class P_Settings
{
    // database
    const MYSQL_HOST = '';
    const MYSQL_PORT = 0000;
    const MYSQL_SCHEMA = 'heds_inv_exp'; # HEdS Inventory expiration
    const MYSQL_ADMIN_ID = '';
    const MYSQL_ADMIN_PASSWORD = '';

    // email account
    const APP_EMAIL = '';
    const APP_EMAIL_PASSWORD = '';
}
