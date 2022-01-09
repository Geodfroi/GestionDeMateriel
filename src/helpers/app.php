<?php

declare(strict_types=1);

################################
## Joël Piguet - 2022.01.09 ###
##############################

namespace app\helpers;

use app\constants\AppPaths;
use Exception;

/**
 * Global config parameters accessible through the application.
 */
class App
{
    private static App $instance;

    /**
     * Associative array extracted from json file, with mode as key.
     */
    private array $content;
    private string $mode;

    /**
     * @param string $mode Mode constants
     */
    function __construct(string $mode)
    {
        $json = file_get_contents(AppPaths::CONFIG_FILE);
        $this->content =  json_decode($json, true);

        $this->mode = $mode;
    }

    private function clearLog()
    {
        $channel = $this->getData('log_channel');
        $path = AppPaths::LOG_FOLDER . DIRECTORY_SEPARATOR . $channel . '.log';
        unlink($path);
    }

    /**
     * @param string $key Data key.
     */
    private function getData(string $key)
    {
        if (!isset($this->content[$this->mode])) {
            throw new Exception("Mode [$this->mode] is not properly defined in [config.json]");
        }
        $mode_array = $this->content[$this->mode];
        if (!isset($mode_array[$key])) {
            throw new Exception("Entry [$key] is not properly defined in mode [$this->mode] in [config.json]");
        }
        return $mode_array[$key];
    }

    /**
     * Set current app mode at entry point of application.
     * 
     * @param $modeUse Mode constants
     */
    public static function setMode(string $mode)
    {
        if (!isset(App::$instance)) {
            App::$instance = new static($mode);
            if (App::$instance->getData('clear_log')) {
                App::$instance->clearLog();
            }

            if (App::$instance->isDebugMode()) {
                Logging::info('mode set', [
                    'channel' => App::$instance->logChannel(),
                    'sqlite' => App::$instance->useSQLite(),
                ]);
            }
        }
    }

    public static function useSQLite(): bool
    {
        return App::$instance->getData("use_sqlite");
    }

    public static function logChannel(): string
    {
        return App::$instance->getData("log_channel");
    }

    public static function isDebugMode(): bool
    {
        return App::$instance->getData("debug_mode");
    }
}

    // /**
    //  * @param string Global Logging channel. Use LogChannel const.
    //  * @param bool $use_sqlite Use local sqlite db instead of MySQL for testing purposes.
    //  * @param bool $debug_mode Set debug mode to true enabling debug page display in html and logging.
    //  */
    // public static function setConfig(string $log_channel, bool $use_sqlite, bool $debug_mode)
    // {
    //     $app = App::getInstance(false);
    //     $app->use_sqlite = $use_sqlite;
    //     $app->log_channel = $log_channel;
    //     $app->debug_mode = $debug_mode;
    // }

        // /**
    //  * @param $mode Current mode; Use Mode constants
    //  */
    // private static function getInstance(int $mode)
    // {
    //     static $instance;
    //     if (is_null($instance)) {
    //         $instance = new static();
    //     }
    //     return $instance;
    // }
