<?php

declare(strict_types=1);

################################
## Joël Piguet - 2021.11.15 ###
##############################

namespace models;

use \DateTime;

class User
{
    private int $id;

    private string $email;

    private string $password;

    private DateTime $creation_date;

    private DateTime $last_login;

    private bool $is_admin;

    /**
     * construct user from db from an associative array
     */
    public function __construct(array $input)
    {
        $this->id = (int)($input['id'] ?? 0);
        $this->email = (string)($input['email'] ?? '');
        $this->password = (string)($input['password'] ?? '');
        $this->creation_date = new DateTime($input['creation_date']);
        $this->last_login = new DateTime($input['last_login']);
        $this->is_admin = (bool)$input['is_admin'];
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
        return sprintf('User %04d> %s, created: %s, last login: %s%s', $this->id, $this->email, $this->creation_date->format('Y.m.d'), $this->last_login->format('Y.m.d H:i:s'), $this->is_admin ? ' (has admin privileges)' : '');
    }
}
