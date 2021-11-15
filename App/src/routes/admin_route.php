<?php

################################
## Joël Piguet - 2021.11.15 ###
##############################

namespace routes;

/**
 * Route class containing behavior linked to admin_template. This route handles all admin related tasks.
 */
class AdminRoute extends BaseRoute
{
    function __construct()
    {
        parent::__construct('admin_template');
    }

    public function getBodyContent(): string
    {
        return $this->renderTemplate();
    }
}
