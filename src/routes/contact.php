<?php

################################
## Joël Piguet - 2021.11.24 ###
##############################

namespace app\routes;

/**
 * Route class containing logic when the user click on the 'contact-us' link in the footer.
 */
class Contact extends BaseRoute
{
    function __construct()
    {
        parent::__construct('contact_template', CONTACT);
    }

    public function getBodyContent(): string
    {
        return $this->renderTemplate();
    }
}