CREATE SCHEMA IF NOT EXISTS HEdS_inv_exp;

USE HEdS_inv_exp;

-- Inventory item
CREATE TABLE IF NOT EXISTS `articles` (
    `id`                int             UNSIGNED NOT NULL  AUTO_INCREMENT PRIMARY KEY,
    `article_name`      varchar(255)    NOT NULL,
    `comments`          varchar(255),
    `creation_date`     timestamp       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `expiration_date`   timestamp       NOT NULL,
    `location`          varchar(255)    NOT NULL,
    `user_id`           int             NOT NULL
 );

-- App Users
CREATE TABLE IF NOT EXISTS `users` (
    `id`            int             UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `alias`         varchar(255)    UNIQUE,
    `email`         varchar(255)    NOT NULL UNIQUE,
    `contact_email` varchar(255),
    `contact_delay` varchar(255)    NOT NULL DEFAULT '3-14',
    `password`      varchar(255)    NOT NULL,
    `creation_date` timestamp       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `last_login`    timestamp       DEFAULT CURRENT_TIMESTAMP,
    `is_admin`      boolean         DEFAULT false
);

-- Location Presets
CREATE TABLE IF NOT EXISTS `locations` (
    `id`            int             UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `str_content`   varchar(255)    NOT NULL
);
