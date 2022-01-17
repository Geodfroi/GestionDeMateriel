<?php

declare(strict_types=1);

################################
## Joël Piguet - 2022.01.17 ###
##############################

namespace app\constants;

/**
 * App settings
 */
class Settings
{

    /**
     * If DEBUG_MODE is active, all emails are sent from and to this address.
     */
    const DEBUG_EMAIL = 'innov.heds@gmail.com';
    const APP_NAME = "HEdS Gestionnaire d'inventaire";

    const APP_URL_DEBUG = "http://localhost:8085/";
    /**
     * Used as hyperlink in emails; must be set to proper url once the project is online. 
     */
    const APP_URL = '';
    const LAST_MODIFICATION = '09 janvier 2022';

    const ALIAS_MIN_LENGHT = 6;
    const ARTICLE_NAME_MIN_LENGHT = 6;
    const ARTICLE_NAME_MAX_LENGTH = 40;

    const ARTICLE_COMMENTS_MAX_LENGHT = 240;
    const ARTICLE_DATE_FUTURE_LIMIT = '2050-01-01';

    const LOCATION_MIN_LENGHT = 6;
    const LOCATION_MAX_LENGHT = 60;

    const DEFAULT_PASSWORD_LENGTH = 12;
    const USER_PASSWORD_MIN_LENGTH = 8;

    const BACKUP_FILES_MAX = 14;

    const TABLE_DISPLAY_COUNT = 20;
}
