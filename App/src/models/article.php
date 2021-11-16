<?php

declare(strict_types=1);

################################
## Joël Piguet - 2021.11.16 ###
##############################

namespace models;

use helpers\DateFormatter;

class Article
{
    const NAME_MAX_LENGTH = 20;
    const LOCATION_MAX_LENGHT = 40;
    const COMMENTS_MAX_LENGHT = 240;

    private int $id;

    private int $user_id;

    private string $article_name;

    private string $location;

    /**
     * @param int UTC time ellapsed in seconds since January 1 1970 00:00:00 GMT.
     */
    private int $expiration_date;

    /**
     * @param int UTC time ellapsed in seconds since January 1 1970 00:00:00 GMT.
     */
    private int $creation_date;

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
        $instance->expiration_date = DateFormatter::toUnixTime($input['expiration_date']);
        $instance->creation_date = DateFormatter::toUnixTime($input['creation_date']);
        return $instance;
    }

    /**
     * Create an article instance from input form.
     * 
     * @param int $user_id The article's owner's id.
     * @param string $article_name The article's designation in the database.
     * @param string $location The article's storage location.
     * @param int $expiration_date Expiration date of the stored article in UTC format.
     * @return Article An article instance.
     */
    public static function fromForm(int $user_id, string $article_name, string $location, int $expiration_date, string $comments): Article
    {
        $instance = new self();
        $instance->id = -1;
        $instance->user_id = $user_id;
        $instance->article_name = $article_name;
        $instance->location = $location;
        $instance->comments = $comments;
        $instance->expiration_date = $expiration_date;
        $instance->creation_date = time();
        return $instance;
    }

    public function getExpirationDate(): int
    {
        return $this->expiration_date;
    }

    public function getUser_id(): int
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

    public function __toString(): string
    {
        return sprintf('Article %04d> %s in %s, expires %s; user_id: %04d; created: %s;', $this->id,  $this->article_name, $this->location, DateFormatter::printSQLTimestamp($this->expiration_date, true), $this->user_id, DateFormatter::printSQLTimestamp($this->creation_date));
    }
}
