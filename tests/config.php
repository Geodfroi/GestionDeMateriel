<?php

################################
## Joël Piguet - 2022.03.10 ###
##############################

/**
 * Verbose logging to help debugging.
 */
const DEBUG_MODE = true;

/**
 * "Name log file in log folder.
 */
const LOG_CHANNEL = "test";

/**
 * Use local sqlite db instead of mySql for testing the application.
 */
const USE_SQLITE = false;

const APP_NAME = "HEdS Gestionnaire d'inventaire";

/**
 * Used as hyperlink in emails; must be set to proper url once the project is online. 
 */
const APP_URL = 'http://campus.hesge.ch/innovations-pedagogiques-heds/gestion-inventaire/';
const LAST_MODIFICATION = '11 mars 2022';

/**
 * Time until alert is dismissed in milliseconds.
 */
const ALERT_TIMER = 2500;

const ARTICLE_NAME_MIN_LENGHT = 6;
const ARTICLE_NAME_MAX_LENGTH = 40;
const ARTICLE_COMMENTS_MAX_LENGHT = 240;
const ARTICLE_DATE_FUTURE_LIMIT = '2050-01-01';
const ARTICLE_LOCATION_MIN_LENGHT = 6;
const ARTICLE_LOCATION_MAX_LENGHT = 60;

const USER_ALIAS_MIN_LENGHT  = 4;
const USER_PASSWORD_DEFAULT_LENGTH = 12;
const USER_PASSWORD_MIN_LENGTH = 8;

/**
 * # of backup db files to conserve on disk.
 */
const BACKUP_FILES_MAX = 14;

/**
 * # of log files to conserve for each log channel.
 */
const LOG_FILES_MAX = 7;

/**
 * # of elements displayed by default in a table.
 */
const TABLE_DISPLAY_COUNT = 10;
