<?php

declare(strict_types=1);

################################
## Joël Piguet - 2022.03.15 ###
##############################

namespace app\helpers;

use app\constants\LogInfo;
use app\constants\Session;
use app\helpers\Logging;
use app\models\User;
use PhpParser\Node\Stmt\Break_;

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

        $str = json_encode($json);
        if (!$str) {
            return "";
        }
        header('Content-Type: application/json; charset=utf-8');
        return $str;
    }

    /**
     * Instruct js fetch function to redirect to url.
     * 
     * @param string $url The redirection path. Use the constants in Routes class to avoir typing mistakes.
     * @param string $alert_type Optional alert type. Use AlertType const.
     * @param string $alert_msg Optional alert message to be displayed after redirection.
     * @return string json response.
     */
    public static function redirect(string $url, string $alert_type = "", string $alert_msg = ""): string
    {
        if (strlen($alert_type) != 0 && strlen($alert_msg) != 0) {
            Util::storeAlert($url, $alert_type, $alert_msg);
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
        return json_decode($json, true);
    }
}
