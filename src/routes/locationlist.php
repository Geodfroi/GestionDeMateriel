<?php

################################
## Joël Piguet - 2021.12.02 ###
##############################

namespace app\routes;

use app\constants\Alert;
use app\constants\AlertType;
use app\constants\Error;
use app\constants\Route;
use app\helpers\Authenticate;
use app\helpers\Database;
use app\helpers\Util;

class LocationList extends BaseRoute
{
    function __construct()
    {
        parent::__construct('location_presets_template', Route::LOCAL_PRESETS);
    }

    public function getBodyContent(): string
    {
        if (!Authenticate::isLoggedIn()) {
            $this->requestRedirect(Route::LOGIN);
        }

        if (isset($_GET['delete'])) {

            if (Database::locations()->delete($_GET['delete'])) {
                $this->setAlert(AlertType::SUCCESS, Alert::LOC_PRESET_REMOVE_SUCCESS);
            } else {
                $this->setAlert(AlertType::FAILURE, Alert::LOC_PRESET_REMOVE_FAILURE);
            }
            goto end;
        }

        if (isset($_GET['update'])) {
            $item = Database::locations()->queryById($_GET['update']);
            if ($item) {
                $selected = $item->getId();
                $location_field = $item->getContent();
            }
            goto end;
        }

        if (isset($_POST['add-new'])) {
            if (Util::validateLocation($this, $location_field)) {

                if (Database::locations()->contentExists($location_field)) {
                    $this->setError('location', Error::LOCATION_PRESET_EXISTS);
                } else {
                    if (Database::locations()->insert($location_field)) {
                        $location_field = '';
                    } else {
                        $this->setAlert(AlertType::FAILURE, Alert::LOCATION_PRESET_INSERT);
                    }
                }
            }
            goto end;
        }

        if (isset($_POST['update'])) {
            $id = intval($_POST['id']);

            if (!Util::validateLocation($this, $location_field)) {
                $selected =  $id; // $location_field will still be filled -> stay in update mode.
                goto end;
            }

            $former = Database::locations()->queryById($id);
            error_log('c: ' . $former->getContent() . ' - ' . $location_field);

            if (strcasecmp($location_field, $former->getContent()) == 0) {
                $location_field = '';
                goto end;
            }

            if (Database::locations()->contentExists($location_field)) {
                $this->setError('location', Error::LOCATION_PRESET_EXISTS);
                $selected =  $id; // $location_field will still be filled -> stay in update mode.
                goto end;
            }

            if (Database::locations()->update($id, $location_field)) {
                $this->setAlert(AlertType::SUCCESS, Alert::LOC_PRESET_UPDATE_SUCCESS);
                $location_field = ''; // $selected = 0 -> return to normal list display.
                goto end;
            }
        }

        end:
        $locations = Database::locations()->queryAll();

        return $this->renderTemplate([
            'selected' => $selected ?? 0,
            'location_field' => $location_field ?? '',
            'locations' => $locations ?? []
        ]);
    }
}
