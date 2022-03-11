<?php

declare(strict_types=1);

################################
## Joël Piguet - 2022.03.11 ###
##############################

namespace app\models;

use DateTime;

use app\helpers\Convert;
use app\helpers\Database;

class Article
{
    /**
     * int 
     */
    private $id;

    /**
     * string 
     */
    private $article_name;

    /**
     * string Free comments set by the user appended to the email remainder sent when the expiration date is close.
     */
    private $comments;

    /**
     * string
     */
    private  $location;

    /**
     * DateTime
     */
    private $creation_date;

    /**
     * DateTime
     */
    private $expiration_date;

    /**
     * int
     */
    private $user_id;

    /**
     * Load article instance from database row.
     * 
     * @param array $input Input from database.
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

        $instance->expiration_date = Convert::toDateTime($input['expiration_date']);
        $instance->creation_date = Convert::toDateTime($input['creation_date']);

        return $instance;
    }

    /**
     * Create an article instance from input form waiting to be inserted into db.
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
        $instance->expiration_date = Convert::toDateTime($expiration_date);
        $instance->creation_date = new DateTime();
        return $instance;
    }

    public function getCreationDate(): DateTime
    {
        return $this->creation_date;
    }

    public function getArticleName(): string
    {
        return $this->article_name;
    }

    public function getComments(): string
    {
        return $this->comments;
    }

    public function getExpirationDate(): DateTime
    {
        return $this->expiration_date;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getLocation(): string
    {
        return $this->location;
    }

    /**
     * Get article creator's alias.
     * 
     * @return string Article owner display alias.
     */
    public function getOwner(): string
    {
        $user = Database::users()->queryById($this->user_id);
        if ($user) {
            //take only caracters before @ if it is an email.
            $alias  = explode('@', $user->getAlias())[0];
            return sprintf("%s (%s)", $alias, $this->creation_date->format('d.m.Y'));
        }
        return "Inconnu";
    }

    public function getUserId(): int
    {
        return $this->user_id;
    }

    public function setId($value)
    {
        $this->id = $value;
    }

    /**
     * Return as associative array.
     * 
     * @return array
     */
    public function asArray(): array
    {
        return [
            'id' => $this->id,
            'article-name' => $this->article_name,
            'comments' => $this->comments,
            'location' => $this->location,
            'creation-date' => $this->creation_date->format('Y-m-d'),
            'expiration-date' => $this->expiration_date->format('Y-m-d'),
            'user-id' => $this->user_id
        ];
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
        $this->expiration_date = Convert::toDateTime($expiration_date);
    }

    public function __toString(): string
    {
        return sprintf('[%s] in [%s], expires %s;', $this->article_name, $this->location, $this->expiration_date->format('Y-m-d'));
    }
}
