<?php

################################
## Joël Piguet - 2021.11.14 ###
##############################

namespace helpers;

/**
 * Load a php template in memory and returns a content string.
 *
 * @param string $name The name of the template.
 * @param array $data The variables to be used in php templates.
 */
function renderTemplate(string $name, array $data = []): string
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
