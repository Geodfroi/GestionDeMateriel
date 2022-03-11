<?php

declare(strict_types=1);

################################
## Joël Piguet - 2022.03.10 ###
##############################

namespace app\constants;

class AppPaths
{
    //app folders
    const EMAIL_TEMPLATES = __DIR__ . '/../email_templates';
    const SCRIPTS =  __DIR__ . '/../scripts';
    const TEMPLATES = __DIR__ . '/../page_templates';

    const LOG_FOLDER = __DIR__ . '/../../local/logs';
    const LOCAL_DB_FOLDER = __DIR__ . '/../../local';
    const TEST_DB_FOLDER = __DIR__ . '/../../tests/output';

    //SQL queries
    const SQLITE_TABLES = __DIR__ . '/../sql/tables_sqlite.sql';
    const SQLITE_ENTRIES = __DIR__ . '/../sql/entries_sqlite.sql';

    // config
    // const CONFIG_FILE = __DIR__ . '/../../config.json';

    // sqlite backup
    const BACKUP_FOLDER = __DIR__ . '/../../_backups';
}

if (!is_dir(AppPaths::BACKUP_FOLDER)) {
    mkdir(AppPaths::BACKUP_FOLDER, 0777, true);
}

if (!is_dir(AppPaths::LOG_FOLDER)) {
    mkdir(AppPaths::LOG_FOLDER, 0777, true);
}

if (!is_dir(AppPaths::TEST_DB_FOLDER)) {
    mkdir(AppPaths::TEST_DB_FOLDER, 0777, true);
}
