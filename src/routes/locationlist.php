<?php

################################
## Joël Piguet - 2021.12.17 ###
##############################

namespace app\routes;

use app\constants\Route;
use app\helpers\Authenticate;
use app\helpers\Database;
// use app\helpers\Logging;

class LocationList extends BaseRoute
{
    function __construct()
    {
        parent::__construct(Route::LOCAL_PRESETS, 'location_presets_template', 'location_presets_script');
    }

    public function getBodyContent(): string
    {
        if (!Authenticate::isLoggedIn()) {
            $this->requestRedirect(Route::LOGIN);
        }

        $locations = Database::locations()->queryAll();

        return $this->renderTemplate([
            'locations' => $locations ?? []
        ]);
    }
}
