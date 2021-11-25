<?php

declare(strict_types=1);

################################
## Joël Piguet - 2021.11.25 ###
##############################

namespace helpers;

/**
 * Utility class containing useful static functions.
 */
class Util
{
    /**
     * Get a randomly generated string of set lenght.
     * https://www.w3docs.com/snippets/php/how-to-generate-a-random-string-with-php.html
     * 
     * @param int $lenght String lenght.
     * @return string String of set lenght.
     */
    public static function randomString(int $lenght): string
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $randomString = '';

        for ($i = 0; $i < $lenght; $i++) {
            $index = rand(0, strlen($characters) - 1);
            $randomString .= $characters[$index];
        }

        return $randomString;
    }


    /**
     * Load a php template in memory and returns a content string.
     *
     * @param string $name The name of the template.
     * @param array $data The variables to be used in php templates.
     * 
     * @return string Rendered template as string.
     */
    public static function renderTemplate(string $name, array $data = []): string
    {
        // extract array variables into the local scope so they can be to be used in the template scripts.
        extract($data, EXTR_OVERWRITE);
        // start buffering the string;
        ob_start();
        // load file content at path and resolve php script to a string in the buffer;
        require TEMPLATES_PATH . DIRECTORY_SEPARATOR . $name . '.php';
        // flush the buffer content to the variable
        $rendered = ob_get_clean();

        return (string)$rendered;
    }
}

/**
 * Utility class containing static functions useful for template editing.
 */
class TUtil
{
    /**
     * Print parameter to form while escaping caracters.
     * 
     * @param mixed $param Param previously entered into form.
     * @return string Escaped email value.
     */
    public static function escape($param): string
    {
        #htmlentities is a php escape function to neutralize potentially harmful script.
        return $param ? htmlentities($param) : '';
    }

    /**
     * Set invalid class tag if the error array contains the key. Set valid tag if the key is defined in values array.
     * 
     * @param array $error Error array.
     * @param string $values Values key.
     * @param mixed $key Value key
     * @return string Class tag or empty string.
     */
    public static function showValid(array $error, array $values, string $key): string
    {
        if (isset($error[$key]))
            return ' is-invalid';

        if ($values[$key])
            return ' is-valid';
        return '';
    }
}
