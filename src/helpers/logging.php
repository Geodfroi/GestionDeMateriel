<?php

declare(strict_types=1);

################################
## Joël Piguet - 2021.12.12 ###
##############################

namespace app\helpers;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

/**
 * Wrapper for Monolog php framework; used to log to file.
 * https://github.com/Seldaek/monolog/blob/main/doc/01-usage.md
 * https://www.scalyr.com/blog/getting-started-quickly-with-php-logging/
 */
class Logging
{
    private Logger $logger;

    function __construct()
    {
        $this->logger = new Logger('app');
        $this->logger->pushHandler(new StreamHandler(__DIR__ . '/../../logs/app.log', Logger::DEBUG));
    }

    /**
     * Simpleton pattern creating app log channel
     */
    public static function app()
    {
        static $instance;
        if (is_null($instance)) {
            $instance = new Logger('app');
            $instance->pushHandler(new StreamHandler(__DIR__ . '/../../logs/app.log', Logger::DEBUG));
        }
        return $instance;
    }

    /**
     * Simpleton pattern creating server log channel
     */
    public static function server()
    {
        static $instance;
        if (is_null($instance)) {
            $instance = new Logger('server');
            $instance->pushHandler(new StreamHandler(__DIR__ . '/../../logs/server.log', Logger::DEBUG));
        }
        return $instance;
    }
}
