<?php

declare(strict_types=1);

################################
## Joël Piguet - 2021.11.17 ###
##############################

namespace helpers;

/**
 * Utility class containing static functions for editing template.
 */
class TemplateUtil
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
    public static function setValidity(array $error, array $values, string $key): string
    {
        if (isset($error[$key]))
            return ' is-invalid';

        if ($values[$key])
            return ' is-valid';
        return '';
    }
}
