<?php

declare(strict_types=1);

################################
## Joël Piguet - 2021.11.23 ###
##############################

namespace models;

use DateTime;

class User
{
    private int $id;

    private string $email;

    private string $password;

    private DateTime $creation_date;

    private DateTime $last_login;

    private bool $is_admin;

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
        $instance->password = (string)($input['password'] ?? '');

        $instance->creation_date = DateTime::createFromFormat('Y-m-d H:i:s', $input['creation_date']);
        $instance->last_login =   DateTime::createFromFormat('Y-m-d H:i:s', $input['last_login']);

        $instance->is_admin = (bool)$input['is_admin'];
        return $instance;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Check password against hash password field contained in user.
     * 
     * @param string $password Input password in plain text.
     * @return bool True if password is correct.
     */
    public function verifyPassword(string $password): bool
    {
        // password_verify transforms plain text $password into hash password of 60 caracters, then compares it to the hash value contained in the user password property.
        return password_verify($password, $this->password);
    }

    public function __toString(): string
    {
        return sprintf('User %04d> %s, created: %s, last login: %s%s', $this->id, $this->email, $this->creation_date->format('Y-m-d'), $this->last_login->format('Y-m-d'), $this->is_admin ? ' (has admin privileges)' : '');
    }
}
