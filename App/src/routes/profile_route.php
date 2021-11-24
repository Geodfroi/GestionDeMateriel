<?php

################################
## Joël Piguet - 2021.11.15 ###
##############################

namespace routes;

/**
 * Route class containing behavior linked to profile_template. This route displays user info.
 */
class ProfileRoute extends BaseRoute
{
    function __construct()
    {
        parent::__construct('profile_template', Routes::PROFILE);
    }

    public function getBodyContent(): string
    {
        return $this->renderTemplate();
    }
}
