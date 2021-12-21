<?php

declare(strict_types=1);

################################
## Joël Piguet - 2021.12.21 ###
##############################

namespace app\constants;

class AppPaths
{
    //app folders
    const EMAIL_TEMPLATES = __DIR__ . '/../email_templates';
    const TEMPLATES = __DIR__ . '/../page_templates';
    const LOG_FOLDER = __DIR__ . '/../../local/logs';
    const BACKUPS_FOLDER = __DIR__ . '/../../local/backups';
    const TEST_DB_FOLDER = __DIR__ . '/../../local/database';

    //SQL queries
    const SQLITE_TABLES = __DIR__ . '/../sql/tables_sqlite.sql';
    const SQLITE_ENTRIES = __DIR__ . '/../sql/entries_sqlite.sql';
}
