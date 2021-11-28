<?php

################################
## Joël Piguet - 2021.11.28 ###
##############################

namespace routes;

/**
 * Route class containing behavior linked to profile_template. This route displays user info.
 */
class ProfileRoute extends BaseRoute
{

    function __construct()
    {
        parent::__construct('profile_template', PROFILE);
    }

    public function getBodyContent(): string
    {
        $display = 0;

        $errors = [];

        if (isset($_GET['change_password'])) {
            $display = 1;
        } else if (isset($_GET['add_email'])) {
            $display = 2;
        } else if (isset($_GET['modify_delay'])) {
            $display = 3;
        }

        return $this->renderTemplate([
            'display' => $display,
            'errors' => $errors,
            'values' => [
                'password-first' => $password_first ?? '',
                'password-second' => $password_second ?? '',
            ],
        ]);
    }
}
