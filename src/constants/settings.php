<?php

declare(strict_types=1);

################################
## Joël Piguet - 2021.12.07 ###
##############################

namespace app\constants;

/**
 * App settings
 */
class Settings
{
    const APP_NAME = "HEdS Gestionnaire d'inventaire";
    const APP_FULL_URL = "http://localhost:8085/";
    const LAST_MODIFICATION = '07 décembre 2021';

    const EMAIL_TEMPLATES_PATH = 'email_templates';
    const TEMPLATES_PATH = 'page_templates';

    const ALIAS_MIN_LENGHT = 6;
    const ARTICLE_NAME_MIN_LENGHT = 6;
    const ARTICLE_NAME_MAX_LENGTH = 40;

    const ARTICLE_COMMENTS_MAX_LENGHT = 240;
    const ARTICLE_DATE_FUTURE_LIMIT = '2050-01-01';

    const LOCATION_MIN_LENGHT = 6;
    const LOCATION_MAX_LENGHT = 30;

    const TABLE_DISPLAY_COUNT = 12;

    const DEFAULT_PASSWORD_LENGTH = 12;
    const USER_PASSWORD_MIN_LENGTH = 8;
}
