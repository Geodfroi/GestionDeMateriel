<?php

declare(strict_types=1);

################################
## Joël Piguet - 2022.04.05 ###
##############################

use app\constants\Alert;
use app\constants\AlertType;
use app\constants\LogInfo;
use app\constants\Route;
use app\helpers\Authenticate;
use app\helpers\BaseRoute;
use app\helpers\Database;
use app\helpers\Logging;
use app\helpers\RequestUtil;
use app\helpers\Validation;
use app\helpers\Util;


require_once __DIR__ . '/../loader.php';
require_once __DIR__ . '/../vendor/autoload.php'; // use composer to load autofile.
session_start();

class LocationList extends BaseRoute
{
    function __construct()
    {
        parent::__construct('locationpresets', 'locationpresets_template', 'locationpresets_script');
    }

    public function getBodyContent(): string
    {

        $locations = Database::locations()->queryAll();

        return $this->renderTemplate([
            'locations' => $locations ?? []
        ]);
    }
}

function addLocationPreset($json): string
{
    $content = isset($json['content']) ? $json['content'] : "";
    $warnings = [];

    if ($content_warning = Validation::validateLocationPreset($content)) {
        $warnings['content'] = $content_warning;
        return RequestUtil::issueWarnings($json, $warnings);
    }

    if (Database::locations()->insert($content)) {

        Logging::info(LogInfo::LOCATION_CREATED, [
            'user-id' => Authenticate::getUserId(),
            'content' => $content
        ]);
        return RequestUtil::redirect(Route::LOCAL_PRESETS);
    }
    return RequestUtil::redirect(Route::LOCAL_PRESETS, AlertType::FAILURE, Alert::LOCATION_PRESET_INSERT);
}

function updateLocationPreset($json): string
{
    $id = intval($json['id']);
    $content = isset($json['content']) ? $json['content'] : "";
    $warnings = [];

    $former_content = Database::locations()->queryById($id)->getContent();

    // no change to content
    if (strcasecmp($content, $former_content) == 0) {
        return RequestUtil::redirect(Route::LOCAL_PRESETS);
    }

    // strcasecmp must be placed before, otherwise validation will always be invalid and show LOCATION_PRESET_EXISTS warning.
    if ($content_warning = Validation::validateLocationPreset($content)) {
        $warnings['content'] = $content_warning;
        return RequestUtil::issueWarnings($json, $warnings);
    }

    if (Database::locations()->update($id, $content)) {

        Logging::info(LogInfo::LOCATION_UPDATED, [
            'user-id' => Authenticate::getUserId(),
            'former-value' => $former_content,
            'new-value' => $content
        ]);
        return RequestUtil::redirect(Route::LOCAL_PRESETS, AlertType::SUCCESS, Alert::LOC_PRESET_UPDATE_SUCCESS);
    }
    return RequestUtil::redirect(Route::LOCAL_PRESETS, AlertType::FAILURE, Alert::LOC_PRESET_UPDATE_FAILURE);
}

Logging::debug("locationpresets route");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $json = RequestUtil::retrievePOSTData();
    Logging::debug("locationpresets POST request", $json);

    if (isset($json['add-loc-preset'])) {
        echo addLocationPreset($json);
    } else if (isset($json['update-loc-preset'])) {
        echo updateLocationPreset($json);
    }
} else {
    if (!Authenticate::isLoggedIn()) {
        Util::requestRedirect(Route::LOGIN);
    } else {
        echo (new LocationList())->renderRoute();
    }
}
