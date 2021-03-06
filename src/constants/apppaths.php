<?php

declare(strict_types=1);

################################
## Joël Piguet - 2022.04.04 ###
##############################

namespace app\constants;

class AppPaths
{
    //app folders
    const TEMPLATES = __DIR__ . '/../app_templates';

    // main script
    const MAIN_SCRIPT =  __DIR__ . '/../../static/js/main_script.js';

    const LOG_FOLDER = __DIR__ . '/../../_local/logs';
    const LOCAL_DB_FOLDER = __DIR__ . '/../../_local';
    const TEST_DB_FOLDER = __DIR__ . '/../../tests/output';

    //SQL queries
    const SQLITE_TABLES = __DIR__ . '/../sql/tables_sqlite.sql';
    const SQLITE_ENTRIES = __DIR__ . '/../sql/entries_sqlite.sql';

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
