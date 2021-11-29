CREATE SCHEMA IF NOT EXISTS HEdS_inv_exp;

USE HEdS_inv_exp;

-- App Users
CREATE TABLE `users` (
    `id`            int             UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `email`         varchar(255)    NOT NULL UNIQUE,
    `alt_email`     varchar(255)
    `password`      varchar(255)    NOT NULL,
    `creation_date` timestamp       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `last_login`    timestamp       DEFAULT CURRENT_TIMESTAMP,
    `is_admin`      boolean         DEFAULT false
);

-- Inventory item
CREATE TABLE `articles` (
    `id`                int             UNSIGNED NOT NULL  AUTO_INCREMENT PRIMARY KEY,
    `user_id`           int             NOT NULL,
    `article_name`      varchar(255)    NOT NULL,
    `location`          varchar(255)    NOT NULL,
    `comments`          varchar(255),
    `expiration_date`   timestamp       NOT NULL,
    `creation_date`     timestamp       NOT NULL DEFAULT CURRENT_TIMESTAMP
);
