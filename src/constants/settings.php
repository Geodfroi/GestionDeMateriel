<?php

declare(strict_types=1);

################################
## Joël Piguet - 2021.12.20 ###
##############################

namespace app\constants;

/**
 * App settings
 */
class Settings
{
    /**
     * Display debug options.
     */
    const DEBUG_MODE = true;

    /**
     * If DEBUG_MODE is active, all emails are sent from and to this address.
     */
    const DEBUG_EMAIL = 'innov.heds@gmail.com';

    const APP_NAME = "HEdS Gestionnaire d'inventaire";

    /**
     * Used as hyperlink in emails; must be set to proper url once the project is online. 
     */
    const APP_FULL_URL = Settings::DEBUG_MODE ? "http://localhost:8085/" : '';
    const LAST_MODIFICATION = '20 décembre 2021';

    const ALIAS_MIN_LENGHT = 6;
    const ARTICLE_NAME_MIN_LENGHT = 6;
    const ARTICLE_NAME_MAX_LENGTH = 40;

    const ARTICLE_COMMENTS_MAX_LENGHT = 240;
    const ARTICLE_DATE_FUTURE_LIMIT = '2050-01-01';

    const LOCATION_MIN_LENGHT = 6;
    const LOCATION_MAX_LENGHT = 30;

    const TABLE_DISPLAY_COUNT = 10;

    const DEFAULT_PASSWORD_LENGTH = 12;
    const USER_PASSWORD_MIN_LENGTH = 8;
}
