<?php

declare(strict_types=1);

################################
## Joël Piguet - 2021.12.07 ###
##############################

namespace app\constants;

/**
 * Private setting not to be stored in github. Fill-in and remove '_template suffix.
 */
class P_Settings
{
    // database
    const HOST = '';
    const PORT = 0000;
    const DB_NAME = 'heds_inv_exp'; # HEdS Inventory expiration
    const DB_ADMIN_ID = '';
    const DB_ADMIN_PASSWORD = '';

    //email account
    const APP_EMAIL = '';
    const APP_EMAIL_PASSWORD = '';
}
