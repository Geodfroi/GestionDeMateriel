CREATE SCHEMA IF NOT EXISTS HEdS_inv_exp;

USE HEdS_inv_exp;

-- App Users
CREATE TABLE `users` (
    `id`            int             UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `email`         varchar(255)    NOT NULL UNIQUE,
    `password`      varchar(255)    NOT NULL,
    `creation_date` datetime        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `is_admin`      boolean         DEFAULT false
);

-- Inventory item
CREATE TABLE `items` (
    `id`                int             UNSIGNED NOT NULL  AUTO_INCREMENT PRIMARY KEY,
    `user_id`           int             NOT NULL,
    `item_name`         varchar(255)    NOT NULL,
    `location`          varchar(255)    NOT NULL,
    `expiration_date`   datetime,
    `creation_date`     datetime        NOT NULL DEFAULT CURRENT_TIMESTAMP
);
