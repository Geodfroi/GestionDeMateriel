<?php

declare(strict_types=1);

################################
## Joël Piguet - 2021.12.15 ###
##############################

namespace app\helpers;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

use app\constants\AppPaths;
use app\constants\LogChannel;

/**
 * Wrapper for Monolog php framework; used to log to file.
 * https://github.com/Seldaek/monolog/blob/main/doc/01-usage.md
 * https://www.scalyr.com/blog/getting-started-quickly-with-php-logging/
 */
class Logging
{
    private static array $channels = [];


    private static function getChannel(string $channel)
    {
        if (!array_key_exists($channel, Logging::$channels)) {
            $channels[$channel] = new Logger($channel);
            $channels[$channel]->pushHandler(new StreamHandler(AppPaths::LOG_FOLDER . DIRECTORY_SEPARATOR . "$channel.log", Logger::DEBUG));
        }
        return  $channels[$channel];
    }

    /**
     * Wrapper for Monolog Logger debug method.
     * 
     * @param string $msg Log message.
     * @param array $context Log context array.
     * @param string $channel Logging channel. Use LogChannel const.
     */
    public static function debug(string $msg, array $context = [], string $channel = LogChannel::APP)
    {
        Logging::getChannel($channel)->debug($msg, $context);
    }

    /**
     * Wrapper for Monolog Logger error method.
     * 
     * @param string $msg Log message.
     * @param array $context Log context array.
     * @param string $channel Logging channel. Use LogChannel const.
     */
    public static function error(string $msg, array $context = [], string $channel = LogChannel::APP)
    {
        Logging::getChannel($channel)->error($msg, $context);
    }

    /**
     * Wrapper for Monolog Logger info method.
     * 
     * @param string $msg Log message.
     * @param array $context Log context array.
     * @param string $channel Logging channel. Use LogChannel const.
     */
    public static function info(string $msg, array $context = [], string $channel = LogChannel::APP)
    {
        Logging::getChannel($channel)->info($msg, $context);

        // switch ($channel) {
        //     case LogChannel::SERVER:
        //         Logging::server()->info($msg, $context);
        //         break;
        //     case LogChannel::TEST:
        //         Logging::test()->info($msg, $context);
        //         break;
        //     default:
        //         break;
        // }
        // Logging::app()->info($msg, $context);
    }
}

    // /**
    //  * Simpleton pattern creating app log channel
    //  */
    // private static function app()
    // {
    //     static $instance;
    //     if (is_null($instance)) {
    //         $instance = new Logger('app');
    //         // __DIR__ . '/../../logs/app.log'
    //         $instance->pushHandler(new StreamHandler(AppPaths::LOG_FOLDER . DIRECTORY_SEPARATOR . 'app.log', Logger::DEBUG));
    //     }
    //     return $instance;
    // }