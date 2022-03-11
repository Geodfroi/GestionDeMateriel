<?php

declare(strict_types=1);

################################
## Joël Piguet - 2022.03.11 ###
##############################

namespace app\models;

/**
 * A object wrapper for string associating an id to a string content.
 */
class StringContent
{
    /**
     * int
     */
    private $id;

    /**
     * string
     */
    private $content;

    /**
     * Load StringContent instance from database row.
     * 
     * @param array $input Input from database.
     * @return StringContent Instance of stringContent.
     */
    public static function fromDatabaseRow(array $input): StringContent
    {
        $instance = new self();
        $instance->id = (int)($input['id'] ?? 0);
        $instance->content = (string)($input['str_content'] ?? '');
        return $instance;
    }

    /**
     * Create a StringContent instance from input form waiting to be inserted into db.
     * 
     * @param string $content string from from.
     * @return StringContent Instance of stringContent.
     */
    public static function fromForm(int $id, string $content): StringContent
    {
        $instance = new self();
        $instance->id = $id;
        $instance->content = $content;
        return $instance;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getContent(): string
    {
        return $this->content ?? '';
    }

    public function __toString(): string
    {
        return sprintf('%s: %s', $this->id, $this->content);
    }
}
