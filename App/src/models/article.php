<?php

declare(strict_types=1);

################################
## Joël Piguet - 2021.11.24 ###
##############################

namespace models;

use DateTime;

class Article
{
    private int $id;

    private int $user_id;

    private string $article_name;

    private string $location;

    private DateTime $expiration_date;

    private DateTime $creation_date;


    /**
     * Free comments set by the user appended to the email remainder sent when the expiration date is close.
     */
    private string $comments;

    /**
     * Load article instance from database row.
     * 
     * @return Article An article instance.
     */
    public static function fromDatabaseRow(array $input): Article
    {
        $instance = new self();
        $instance->id = (int)($input['id'] ?? 0);
        $instance->user_id = (int)($input['user_id'] ?? 0);
        $instance->article_name = (string)($input['article_name'] ?? '');
        $instance->location = (string)($input['location'] ?? '');
        $instance->comments = (string)($input['comments'] ?? '');

        $instance->expiration_date = DateTime::createFromFormat('Y-m-d H:i:s', $input['expiration_date']);
        $instance->creation_date = DateTime::createFromFormat('Y-m-d H:i:s', $input['creation_date']);

        return $instance;
    }

    /**
     * Create an article instance from input form.
     * 
     * @param int $user_id The article's owner's id.
     * @param string $article_name The article's designation in the database.
     * @param string $location The article's storage location.
     * @param string $expiration_date Expiration date of the stored article as a string.
     * @param string $comments User comments on article.
     * @return Article An article instance.
     */
    public static function fromForm(int $user_id, string $article_name, string $location, string $expiration_date, string $comments = ''): Article
    {
        $instance = new self();
        $instance->user_id = $user_id;
        $instance->article_name = $article_name;
        $instance->location = $location;
        $instance->comments = $comments;
        $instance->expiration_date = DateTime::createFromFormat('Y-m-d', $expiration_date);
        $instance->creation_date = new DateTime();
        return $instance;
    }

    public function getExpirationDate(): DateTime
    {
        return $this->expiration_date;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getUserId(): int
    {
        return $this->user_id;
    }

    public function getLocation(): string
    {
        return $this->location;
    }

    public function getArticleName(): string
    {
        return $this->article_name;
    }

    public function getComments(): string
    {
        return $this->comments;
    }

    /**
     * Update instance fields with new values. 
     * 
     * @param string $article_name The article's designation in the database.
     * @param string $location The article's storage location.
     * @param string $expiration_date Expiration date of the stored article as a string.
     * @param string $comments User comments on article.
     */
    public function updateFields(string $article_name, string $location, string $expiration_date, string $comments)
    {
        $this->article_name = $article_name;
        $this->location = $location;
        $this->comments = $comments;
        $this->expiration_date = DateTime::createFromFormat('Y-m-d', $expiration_date);
    }

    public function __toString(): string
    {
        return sprintf('Article %04d> [%s] in [%s], expires %s; user_id: %04d; created: %s;', $this->id,  $this->article_name, $this->location, $this->expiration_date->format('Y-m-d'), $this->user_id, $this->creation_date->format('Y-m-d'));
    }
}
