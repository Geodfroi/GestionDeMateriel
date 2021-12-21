<?php

declare(strict_types=1);

################################
## Joël Piguet - 2021.12.21 ###
##############################

namespace app\helpers;

use Exception;

/**
 * Global config parameters accessible through the application.
 */
class App
{
    private bool $use_sqlite;
    private string $log_channel;
    private bool $debug_mode;

    private static function getInstance(bool $must_be_initialised)
    {
        static $instance;
        if (is_null($instance)) {
            if ($must_be_initialised) {
                throw new Exception('App config was not initialised before access.');
            }
            $instance = new static();
        }
        return $instance;
    }

    /**
     * @param string Global Logging channel. Use LogChannel const.
     * @param bool $use_sqlite Use local sqlite db instead of MySQL for testing purposes.
     * @param bool $debug_mode Set debug mode to true enabling debug page display in html and logging.
     */
    public static function setConfig(string $log_channel, bool $use_sqlite, bool $debug_mode)
    {
        $app = App::getInstance(false);
        $app->use_sqlite = $use_sqlite;
        $app->log_channel = $log_channel;
        $app->debug_mode = $debug_mode;
    }

    public static function useSQLite(): bool
    {
        return App::getInstance(true)->use_sqlite;
    }

    public static function logChannel(): string
    {
        return App::getInstance(true)->log_channel;
    }

    public static function isDebugMode(): bool
    {
        return App::getInstance(true)->debug_mode;
    }
}
