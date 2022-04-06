<?php

declare(strict_types=1);

################################
## Joël Piguet - 2022.04.06 ###
##############################
/**
 * This route handle app fetch requests for data from javascript.
 */

use app\helpers\Database;
use app\helpers\Logging;

require_once __DIR__ . '/../loader.php';
require_once __DIR__ . '/../vendor/autoload.php'; // use composer to load autofile.
session_start();

Logging::debug('server data', $_SERVER);

// if ($_SERVER['REQUEST_METHOD'] === 'POST') {
//     echo handlePostRequests();
// } else {
//     Logging::error('Invalid request call');
//     echo 'Invalid request call';
// }

/**
 * Note: Post requests use redirect for redirection (signal javascript to redirect)
 */
function handlePostRequests(): string
{
    // Takes raw data from the request
    $json = file_get_contents('php://input');
    // Converts it into a PHP object
    $data = json_decode($json, true);

    if (!isset($data['req'])) {
        $response = ['error' => '[req] key was not defined in fetch request.'];
        Logging::error('data request error', $response);
        return json_encode($response);
    }

    Logging::debug("Post request to server", ['data' => $data['req']]);

    switch ($data['req']) {
        case 'get-article':
            $article = Database::articles()->queryById(intval($data['id']));
            return json_encode($article->asArray());
        default:
            $response = [
                'error' => '[req] key was not found in fetch request.',
                'req' => $data['req']
            ];
            Logging::error('data request error', $response);
            return json_encode($response);
    }
    return json_encode($data);
}
