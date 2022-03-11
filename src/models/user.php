<?php

declare(strict_types=1);

################################
## Joël Piguet - 2022.03.11 ###
##############################

namespace app\models;

use DateTime;
use app\helpers\Util;
use app\helpers\Convert;

class User
{
    /**
     * int
     */
    private $id;

    /**
     * string Display alias in table instead of e-mail to identify the user.
     */
    private $alias;

    /**
     * string
     */
    private $contact_delay;

    /**
     * string
     */
    private $contact_email;

    /**
     * DateTime
     */
    private $creation_date;

    /**
     * string
     */
    private $login_email;

    /**
     * bool
     */
    private $is_admin;

    /**
     * DateTime 
     * */
    private $last_login;

    /**
     * string Password hash encrypted with PASSWORD_BCRYPT algorithm. No plaintext password are ever stored into the database.
     */
    private $password_hash;

    /**
     * Load user instance from database row.
     * 
     * @param array $input Input from database.
     * @return User A user instance.
     */
    public static function fromDatabaseRow(array $input): User
    {
        $instance = new self();
        $instance->id = (int)($input['id'] ?? 0);
        $instance->alias = (string)($input['alias'] ?? '');
        $instance->contact_delay = (string)($input['contact_delay'] ?? '3-14');
        $instance->contact_email = (string)($input['contact_email'] ?? '');
        $instance->login_email = (string)($input['login_email'] ?? '');
        $instance->password_hash = (string)($input['password'] ?? '');

        $instance->creation_date = Convert::toDateTime($input['creation_date']);
        $instance->last_login = Convert::toDateTime($input['last_login']);

        $instance->is_admin = (bool)$input['is_admin'];
        return $instance;
    }

    /**
     * Create new user from input form waiting to be inserted into db.
     * 
     * @param string $email User login_email.
     * @param string $password User password waiting to be encrypted.
     * @param bool $is_admin True if the new user has admin privileges.
     */
    public static function fromForm(string $login_email, string $plain_password, bool $is_admin = false): User
    {
        $instance = new self();
        $instance->id = -1;
        $instance->contact_email = '';
        $instance->contact_delay = '3-14';
        $instance->login_email = $login_email;
        $instance->alias = $login_email;
        $instance->password_hash = Util::encryptPassword($plain_password);
        $instance->creation_date = new DateTime();
        $instance->last_login = new DateTime();
        $instance->is_admin = $is_admin;

        return $instance;
    }

    public function getAlias(): string
    {
        return $this->alias;
    }

    public function getCreationDate(): DateTime
    {
        return $this->creation_date;
    }

    /**
     * @return array Array of integer values
     */
    public function getContactDelays(): array
    {
        return array_map('intval', explode('-', $this->contact_delay));
    }

    public function getContactEmail(): string
    {
        return $this->contact_email;
    }

    public function getLoginEmail(): string
    {
        return $this->login_email;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getLastLogin(): DateTime
    {
        return $this->last_login;
    }

    /**
     * Addresses where emails will be sent.
     */
    public function getMailingAddresses(): array
    {
        $emails = [];
        array_push($emails, $this->getLoginEmail());
        if ($this->getContactEmail()) {
            array_push($emails, $this->getContactEmail());
        }
        return $emails;
    }

    public function getPassword(): string
    {
        return $this->password_hash;
    }

    public function isAdmin(): bool
    {
        return $this->is_admin;
    }

    public function setId(int $value)
    {
        $this->id = $value;
    }

    /**
     * Return as JSON data.
     */
    public function toJSON()
    {
        $array = [
            'id' => $this->id,
            'alias' => $this->alias,
            'contact-delay' => $this->contact_delay,
            'contact-email' => $this->contact_email,
            'creation-date' => $this->creation_date->format('Y-m-d'),
            'is-admin' => $this->is_admin,
            'last-login' => $this->last_login->format('Y-m-d'),
        ];
        return json_encode($array);
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
        return sprintf('User %04d> %s, created: %s, last login: %s%s', $this->id, $this->login_email, $this->creation_date->format('Y-m-d'), $this->last_login->format('Y-m-d'), $this->is_admin ? ' (has admin privileges)' : '');
    }
}
