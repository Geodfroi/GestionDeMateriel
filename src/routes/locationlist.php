<?php

################################
## Joël Piguet - 2021.12.13 ###
##############################

namespace app\routes;

use app\constants\Alert;
use app\constants\AlertType;
use app\constants\LogInfo;
use app\constants\Route;
use app\constants\Warning;
use app\helpers\Authenticate;
use app\helpers\Database;
use app\helpers\Logging;
use app\helpers\Validation;

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

        $user_id = Authenticate::getUserId();

        if (isset($_GET['delete'])) {

            $former_location = Database::locations()->queryById($_GET['delete']);
            if (Database::locations()->delete($_GET['delete'])) {

                Logging::info(LogInfo::LOCATION_DELETED, [
                    'user-id' => $user_id,
                    'former-value' => $former_location
                ]);

                $this->showAlert(AlertType::SUCCESS, Alert::LOC_PRESET_REMOVE_SUCCESS);
            } else {
                $this->showAlert(AlertType::FAILURE, Alert::LOC_PRESET_REMOVE_FAILURE);
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
            if (Validation::validateLocation($this, $location_field)) {

                if (Database::locations()->contentExists($location_field)) {
                    $this->showWarning('location', Warning::LOCATION_PRESET_EXISTS);
                } else {
                    if (Database::locations()->insert($location_field)) {

                        Logging::info(LogInfo::LOCATION_CREATED, [
                            'user-id' => $user_id,
                            'value' => $location_field
                        ]);

                        $location_field = '';
                    } else {
                        $this->showAlert(AlertType::FAILURE, Alert::LOCATION_PRESET_INSERT);
                    }
                }
            }
            goto end;
        }

        if (isset($_POST['update'])) {
            $id = intval($_POST['id']);

            if (!Validation::validateLocation($this, $location_field)) {
                $selected =  $id; // $location_field will still be filled -> stay in update mode.
                goto end;
            }

            $former = Database::locations()->queryById($id);

            if (strcasecmp($location_field, $former->getContent()) == 0) {
                $location_field = '';
                goto end;
            }

            if (Database::locations()->contentExists($location_field)) {
                $this->showWarning('location', Warning::LOCATION_PRESET_EXISTS);
                $selected =  $id; // $location_field will still be filled -> stay in update mode.
                goto end;
            }

            if (Database::locations()->update($id, $location_field)) {

                Logging::info(LogInfo::LOCATION_UPDATED, [
                    'user-id' => $user_id,
                    'former-value' => $former->getContent(),
                    'new-value' => $location_field
                ]);

                $this->showAlert(AlertType::SUCCESS, Alert::LOC_PRESET_UPDATE_SUCCESS);
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
