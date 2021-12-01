<?php

################################
## Joël Piguet - 2021.12.01 ###
##############################

namespace routes;

use helpers\Authenticate;
use helpers\Database;

class LocationList extends BaseRoute
{
    function __construct()
    {
        parent::__construct(LOC_PRESETS_TEMPLATE, LOCAL_PRESETS);
    }

    public function getBodyContent(): string
    {
        if (!Authenticate::isLoggedIn()) {
            $this->requestRedirect(LOGIN);
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            if (isset($_POST['add-new'])) {
                if ($this->validateNew($location_field)) {;
                    if (Database::locations()->insert($location_field)) {
                    } else {
                        $this->setAlert(AlertType::FAILURE, LOCATION_PRESET_INSERT);
                    }
                }
            }
        }

        $locations = Database::locations()->queryAll();

        return $this->renderTemplate([
            'location_field' => $location_field ?? '',
            'locations' => $locations ?? []
        ]);
    }

    /**
     * New location name validation. Name must be longer than set lenght and not already exist.
     * @return bool True if validated.
     */
    private function validateNew(&$new_location): bool
    {
        $new_location = trim($_POST['location-field']) ?? '';

        if (strlen($new_location) == 0) {
            $this->setError('location-field', LOCATION_PRESET_EMPTY);
            return false;
        }
        if (strlen($new_location) < LOCATION_MIN_LENGHT) {
            $this->setError('location-field', sprintf(LOCATION_TOO_SHORT, LOCATION_MIN_LENGHT));
            return false;
        }

        if (strlen($new_location) > LOCATION_MAX_LENGHT) {
            $this->setError('location-field', sprintf(LOCATION_TOO_LONG, LOCATION_MAX_LENGHT));
            return false;
        }
        if (Database::locations()->contentExists($new_location)) {
            $this->setError('location-field', LOCATION_PRESET_EXISTS);
            return false;
        }

        return true;
    }
}
