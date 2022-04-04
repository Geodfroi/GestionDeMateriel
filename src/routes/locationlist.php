<?php

################################
## Joël Piguet - 2021.12.17 ###
##############################

namespace app\routes;

use app\constants\Route;
use app\helpers\Authenticate;
use app\helpers\BaseRoute;
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

        $locations = Database::locations()->queryAll();

        return $this->renderTemplate([
            'locations' => $locations ?? []
        ]);
    }
}
