<?php

declare(strict_types=1);

################################
## Joël Piguet - 2022.04.06 ###
##############################

namespace app\helpers;

use app\helpers\Logging;

class RequestUtil
{
    /**
     * Send a json response containing invalid form warnings.
     * 
     * @param array $original request data sent back to javascript.
     * @param array $warnings. Warnings associative array with input field as key.
     * @return string json response.
     */
    public static function issueWarnings(array $json, array $warnings): string
    {
        $json['validated'] = false;
        $json['warnings'] = $warnings;

        // header('Content-Type: application/json; charset=utf-8');
        header('Content-Type: application/json');
        $str = json_encode($json);
        return $str ? $str : '{}';
    }

    /**
     * Send a json response to js fetch operation instructing browser to redirect to new url.
     * 
     * @param string $url The redirection path. Use the constants in Routes class to avoir typing mistakes.
     * @param string $alert_type Optional alert type. Use AlertType const.
     * @param string $alert_msg Optional alert message to be displayed after redirection.
     * @return string json response.
     */
    public static function redirectJSON(string $url, string $alert_type = "", string $alert_msg = ""): string
    {
        if (strlen($alert_type) != 0 && strlen($alert_msg) != 0) {
            Util::storeAlert($alert_type, $alert_msg);
        }
        Logging::debug('redirect_url: ' . $url);
        return json_encode(['url' => $url]);
    }

    /**
     * Retrieve JSON data from POST requests.
     * 
     * @return array PHP associative array.
     */
    public static function retrievePOSTData()
    {
        // Takes raw data from the request
        $json = file_get_contents('php://input');
        // Converts it into a PHP object
        $array = json_decode($json, true);
        Logging::debug("retrieved POST data", $array);
        return $array;
    }
}
