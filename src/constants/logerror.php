<?php

declare(strict_types=1);

################################
## Joël Piguet - 2021.12.12 ###
##############################

namespace app\constants;

/**
 * Log error messages.
 */
class LogError
{
    //Database errors
    const ARTICLE_DELETE = 'failure to delete article from database.';
    const ARTICLE_INSERT = 'failure to insert article.';
    const ARTICLE_QUERY = 'failure to retrieve article from database.';
    const ARTICLES_COUNT_QUERY = 'failure to count articles from database.';
    const ARTICLES_QUERY = 'failure to retrieve article list from database.';
    const ARTICLE_UPDATE = 'failure to update article.';
    const CONN_FAILURE = 'Failed to establish SQL connection.';
    const LOCATIONS_CHECK_CONTENT = 'failure to check for content string.';
    const LOCATION_DELETE = 'failure to delete location from database.';
    const LOCATION_INSERT = 'failure to properly insert new location.';
    const LOCATION_QUERY = 'failure to retrieve location from database.';
    const LOCATIONS_QUERY_ALL = 'failure to retrieve locations.';
    const LOCATION_UPDATE = 'failure to update location string correctly.';
    const USER_ALIAS_UPDATE = 'failure to update user alias.';
    const USER_ARTICLES_DELETE = 'failure to delete user articles from database.';
    const USER_CONTACT_UPDATE = 'failure to update user contact email.';
    const USER_DELAY_UPDATE = 'failure to update user contact delay.';
    const USER_DELETE = 'failure to delete user from database.';
    const USER_INSERT = 'failure to insert user.';
    const USER_LOGTIME_UPDATE = 'failure to update user log time.';
    const USER_PASSWORD_UPDATE = 'failure to update user password.';
    const USER_QUERY = 'failure to retrieve user from database.';
    const USERS_COUNT_QUERY = 'failure to count users from database.';
    const USERS_QUERY = 'failure to retrieve user list from database.';
}
