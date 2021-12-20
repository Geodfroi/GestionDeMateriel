<?php

declare(strict_types=1);

################################
## Joël Piguet - 2021.12.20 ###
##############################

namespace app\constants;

class AppPaths
{
    //app folders
    const EMAIL_TEMPLATES = __DIR__ . '/../email_templates';
    const TEMPLATES = __DIR__ . '/../page_templates';
    const LOG_FOLDER = __DIR__ . '/../../local/logs';

    //SQLite db
    const DEBUG_LOCAL_DB = __DIR__ . '/../../local/Database/localDB.SQLite';
    const TEST_UNIT_DB = __DIR__ . '/../../local/Database/testDB.SQLite';

    //SQL queries
    const SQLITE_TABLES = __DIR__ . '/../sql/tables_sqlite.sql';
    const SQLITE_ENTRIES = __DIR__ . '/../sql/entries_sqlite.sql';
}
