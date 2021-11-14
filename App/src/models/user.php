<?php

declare(strict_types=1);

################################
## Joël Piguet - 2021.11.14 ###
##############################

namespace models;

use \DateTime;

class User
{
    /** @var int */
    private $id;

    /** @var string */
    private $email;

    /** @var string */
    private $password;

    /** @var DateTime */
    private $creation_date;

    /** @var bool */
    private $is_admin;

    /**
     * construct user from db from an associative array
     */
    public function __construct(array $input)
    {
        $this->id = (int)($input['id'] ?? 0);
        $this->email = (string)($input['email'] ?? '');
        $this->password = (string)($input['password'] ?? '');
        $this->creation_date = new DateTime($input['creation_date']);
        $this->is_admin = (bool)$input['is_admin'];
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getEmail(): string
    {
        return $this->email;
    }
}
