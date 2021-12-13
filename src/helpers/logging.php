<?php

declare(strict_types=1);

################################
## Joël Piguet - 2021.12.13 ###
##############################

namespace app\helpers;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

use app\constants\LogChannel;

/**
 * Wrapper for Monolog php framework; used to log to file.
 * https://github.com/Seldaek/monolog/blob/main/doc/01-usage.md
 * https://www.scalyr.com/blog/getting-started-quickly-with-php-logging/
 */
class Logging
{
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
     * @param int $channel Logging channel. Use LogChannel const.
     */
    public static function debug(string $msg, array $context = [], int $channel = LogChannel::APP)
    {
        switch ($channel) {
            case LogChannel::SERVER:
                Logging::server()->debug($msg, $context);
                break;
            case LogChannel::TEST:
                Logging::test()->debug($msg, $context);
                break;
            default:
                break;
        }
        Logging::app()->debug($msg, $context);
    }

    /**
     * Wrapper for Monolog Logger error method.
     * @param string $msg Log message.
     * @param array $context Log context array.
     * @param int $channel Logging channel. Use LogChannel const.
     */
    public static function error(string $msg, array $context = [], int $channel = LogChannel::APP)
    {
        switch ($channel) {
            case LogChannel::SERVER:
                Logging::server()->error($msg, $context);
                break;
            case LogChannel::TEST:
                Logging::test()->error($msg, $context);
                break;
            default:
                break;
        }
        Logging::app()->error($msg, $context);
    }

    /**
     * Wrapper for Monolog Logger info method.
     * @param string $msg Log message.
     * @param array $context Log context array.
     * @param int $channel Logging channel. Use LogChannel const.
     */
    public static function info(string $msg, array $context = [], int $channel = LogChannel::APP)
    {
        switch ($channel) {
            case LogChannel::SERVER:
                Logging::server()->info($msg, $context);
                break;
            case LogChannel::TEST:
                Logging::test()->info($msg, $context);
                break;
            default:
                break;
        }
        Logging::app()->info($msg, $context);
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

    /**
     * Simpleton pattern creating server log channel
     */
    private static function test()
    {
        static $instance;
        if (is_null($instance)) {
            $instance = new Logger('test');
            $instance->pushHandler(new StreamHandler(__DIR__ . '/../../logs/test.log', Logger::DEBUG));
        }
        return $instance;
    }
}
