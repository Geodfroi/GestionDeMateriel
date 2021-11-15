<?php

declare(strict_types=1);

################################
## Joël Piguet - 2021.11.15 ###
##############################

namespace models;

use \DateTime;

class Article
{
    private int $id;

    private int $user_id;

    private string $article_name;

    private string $location;

    private DateTime $expiration_date;

    private DateTime $creation_date;


    public function __construct(array $input)
    {
        $this->id = (int)($input['id'] ?? 0);
        $this->user_id = (int)($input['user_id'] ?? 0);
        $this->article_name = (string)($input['article_name'] ?? '');
        $this->location = (string)($input['location'] ?? '');
        $this->expiration_date = new DateTime($input['expiration_date']);
        $this->creation_date = new DateTime($input['creation_date']);
    }

    public function getArticleName()
    {
        return $this->article_name;
    }

    public function getLocation()
    {
        return $this->location;
    }

    /**
     * Print expiration date to display inside a table in DAY-MONTH-YEAR format;
     * 
     * @return string Printed expiration date.
     */
    public function printExpirationDate(): string
    {
        return $this->expiration_date->format('d.m.Y');
    }

    public function __toString(): string
    {
        return sprintf('Article %04d> %s in %s, expires %s; user_id: %04d; created: %s;', $this->id,  $this->article_name, $this->location, $this->expiration_date->format('Y.m.d'), $this->user_id, $this->creation_date->format('Y.m.d'));
    }
}
