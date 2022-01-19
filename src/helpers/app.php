<?php

declare(strict_types=1);

################################
## Joël Piguet - 2022.01.19 ###
##############################

namespace app\helpers;

use DirectoryIterator;
use Exception;

use app\constants\AppPaths;

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
        foreach (new DirectoryIterator(AppPaths::LOG_FOLDER) as $file) {
            if (!$file->isDot()) {
                $file_name = $file->getFilename();
                if (Util::startsWith($file_name, $channel)) {
                    $path = AppPaths::LOG_FOLDER . DIRECTORY_SEPARATOR . $file_name;
                    unlink($path);
                }
            }
        }
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
