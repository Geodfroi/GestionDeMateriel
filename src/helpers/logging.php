<?php

declare(strict_types=1);

################################
## Joël Piguet - 2021.12.13 ###
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
    private static function app()
    {
        static $instance;
        if (is_null($instance)) {
            $instance = new Logger('app');
            $instance->pushHandler(new StreamHandler(__DIR__ . '/../../logs/app.log', Logger::DEBUG));
        }
        return $instance;
    }

    /**
     * Wrapper for Monolog Logger debug method.
     * @param string $msg Log message.
     * @param array $context Log context array.
     * @param string $channel Logging channel.
     */
    public static function debug(string $msg, array $context = [], string $channel = 'app')
    {
        if ($channel === 'server') {
            Logging::server()->debug($msg, $context);
        } else {
            Logging::app()->debug($msg, $context);
        }
    }

    /**
     * Wrapper for Monolog Logger error method.
     * @param string $msg Log message.
     * @param array $context Log context array.
     * @param string $channel Logging channel.
     */
    public static function error(string $msg, array $context = [], string $channel = 'app')
    {
        if ($channel === 'server') {
            Logging::server()->error($msg, $context);
        } else {
            Logging::app()->error($msg, $context);
        }
    }

    /**
     * Wrapper for Monolog Logger info method.
     * @param string $msg Log message.
     * @param array $context Log context array.
     * @param string $channel Logging channel.
     */
    public static function info(string $msg, array $context = [], string $channel = 'app')
    {
        if ($channel === 'server') {
            Logging::server()->info($msg, $context);
        } else {
            Logging::app()->info($msg, $context);
        }
    }

    /**
     * Simpleton pattern creating server log channel
     */
    private static function server()
    {
        static $instance;
        if (is_null($instance)) {
            $instance = new Logger('server');
            $instance->pushHandler(new StreamHandler(__DIR__ . '/../../logs/server.log', Logger::DEBUG));
        }
        return $instance;
    }
}
