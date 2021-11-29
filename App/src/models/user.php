<?php

declare(strict_types=1);

################################
## Joël Piguet - 2021.11.29 ###
##############################

namespace models;

use helpers\Util;
use DateTime;

class User
{
    private int $id;

    private DateTime $creation_date;

    private string $email;

    private bool $is_admin;

    private DateTime $last_login;

    /**
     * Password hash encrypted with PASSWORD_BCRYPT algorithm. No plaintext password are ever stored into the database.
     */
    private string $password_hash;

    /**
     * Load user instance from database row.
     * 
     * @return User A user instance.
     */
    public static function fromDatabaseRow(array $input): User
    {
        $instance = new self();
        $instance->id = (int)($input['id'] ?? 0);
        $instance->email = (string)($input['email'] ?? '');
        $instance->password_hash = (string)($input['password'] ?? '');

        $instance->creation_date = DateTime::createFromFormat('Y-m-d H:i:s', $input['creation_date']);
        $instance->last_login =   DateTime::createFromFormat('Y-m-d H:i:s', $input['last_login']);

        $instance->is_admin = (bool)$input['is_admin'];
        return $instance;
    }

    /**
     * Create new user from input form waiting to be inserted into db.
     * 
     * @param string $email User email.
     * @param string $password User password waiting to be encrypted.
     * @param bool $is_admin True if the new user has admin privileges.
     */
    public static function fromForm(string $email, string $plain_password, bool $is_admin = false): User
    {
        $instance = new self();
        $instance->id = -1;
        $instance->email = $email;
        $instance->password_hash = Util::encryptPassword($plain_password);
        $instance->creation_date = new DateTime();
        $instance->last_login = new DateTime();
        $instance->is_admin = $is_admin;

        return $instance;
    }

    public function getCreationDate(): DateTime
    {
        return $this->creation_date;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getLastLogin(): DateTime
    {
        return $this->last_login;
    }

    public function getPassword(): string
    {
        return $this->password_hash;
    }

    public function isAdmin(): bool
    {
        return $this->is_admin;
    }

    /**
     * Check password against hash password field contained in user.
     * 
     * @param string $plain_password Input password in plain text.
     * @return bool True if password is correct.
     */
    public function verifyPassword(string $plain_password): bool
    {
        error_log('pl: ' . strval(strlen($this->password_hash)));
        // password_verify transforms plain text $password into hash password of 60 caracters, then compares it to the hash value contained in the user password property.
        return password_verify($plain_password, $this->password_hash);
    }

    public function __toString(): string
    {
        return sprintf('User %04d> %s, created: %s, last login: %s%s', $this->id, $this->email, $this->creation_date->format('Y-m-d'), $this->last_login->format('Y-m-d'), $this->is_admin ? ' (has admin privileges)' : '');
    }
}
