<?php

declare(strict_types=1);

################################
## Joël Piguet - 2022.01.08 ###
##############################

namespace app\constants;

class AppPaths
{
    //app folders
    const EMAIL_TEMPLATES = __DIR__ . '/../email_templates';
    const TEMPLATES = __DIR__ . '/../page_templates';
    const LOG_FOLDER = __DIR__ . '/../../local/logs';

    // const BACKUPS_FOLDER =
    const TEST_DB_FOLDER = __DIR__ . '/../../tests/output';
    const LOCAL_DB_FOLDER = __DIR__ . '/../../local';

    //SQL queries
    const SQLITE_TABLES = __DIR__ . '/../sql/tables_sqlite.sql';
    const SQLITE_ENTRIES = __DIR__ . '/../sql/entries_sqlite.sql';

    // config
    const CONFIG_FILE = __DIR__ . '/../../config.json';
}
