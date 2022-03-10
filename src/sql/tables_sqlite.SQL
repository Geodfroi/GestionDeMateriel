CREATE TABLE IF NOT EXISTS articles (
    id                INTEGER         NOT NULL PRIMARY KEY AUTOINCREMENT,
    article_name      varchar(255)    NOT NULL,
    comments          varchar(255),
    creation_date     timestamp       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    expiration_date   timestamp       NOT NULL,
    location          varchar(255)    NOT NULL,
    user_id           INTEGER         NOT NULL);

CREATE TABLE IF NOT EXISTS users (
    id            INTEGER         NOT NULL PRIMARY KEY AUTOINCREMENT,
    alias         varchar(255)    UNIQUE,
    login_email   varchar(255)    NOT NULL UNIQUE,
    contact_email varchar(255),
    contact_delay varchar(255)    NOT NULL DEFAULT '3-14',
    password      varchar(255)    NOT NULL,
    creation_date timestamp       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    last_login    timestamp       DEFAULT CURRENT_TIMESTAMP,
    is_admin      boolean         DEFAULT false);

CREATE TABLE IF NOT EXISTS locations (
    id            INTEGER         NOT NULL PRIMARY KEY AUTOINCREMENT,
    str_content   varchar(255)    NOT NULL);