<?php

declare(strict_types=1);

################################
## Joël Piguet - 2022.03.11 ###
##############################

namespace app\helpers;

use DateTime;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use app\constants\AppPaths;

/**
 * Wrapper for Monolog php framework; used to log to file.
 * https://github.com/Seldaek/monolog/blob/main/doc/01-usage.md
 * https://www.scalyr.com/blog/getting-started-quickly-with-php-logging/
 */
class Logging
{
    private static array $channels = [];

    private static function getChannel(?string $channel)
    {
        $channel = is_null($channel) ? LOG_CHANNEL : $channel;

        if (!array_key_exists($channel, Logging::$channels)) {

            Util::eraseOldFiles(AppPaths::LOG_FOLDER, $channel, 'log', LOG_FILES_MAX);
            $channels[$channel] = new Logger($channel);
            $log_name = sprintf('%s_%s.log', $channel, (new DateTime('now'))->format('Ymd'));
            $channels[$channel]->pushHandler(new StreamHandler(AppPaths::LOG_FOLDER . DIRECTORY_SEPARATOR . $log_name, Logger::DEBUG));
        }
        return $channels[$channel];
    }

    /**
     * Wrapper for Monolog Logger debug method.
     * 
     * @param string $msg Log message.
     * @param array $context Log context array.
     * @param string $channel Override Global Logging channel. Use LogChannel const.
     */
    public static function debug(string $msg, array $context = [], string $channel = null)
    {
        Logging::getChannel($channel)->debug($msg, $context);
    }

    /**
     * Wrapper for Monolog Logger error method.
     * 
     * @param string $msg Log message.
     * @param array $context Log context array.
     * @param string $channel Override Global Logging channel. Use LogChannel const.
     */
    public static function error(string $msg, array $context = [], string $channel = null)
    {
        Logging::getChannel($channel)->error($msg, $context);
    }

    /**
     * Wrapper for Monolog Logger info method.
     * 
     * @param string $msg Log message.
     * @param string $channel Override Global Logging channel. Use LogChannel const.
     */
    public static function info(string $msg, array $context = [], string $channel = null)
    {
        Logging::getChannel($channel)->info($msg, $context);
    }
}
