<?php

declare(strict_types=1);

################################
## Joël Piguet - 2021.12.21 ###
##############################
// periodically iterate through articles and send reminder emails when they are close to peremption

require_once __DIR__ . '/../vendor/autoload.php'; // use composer to load autofile.

use app\constants\LogChannel;
use app\helpers\App;
use app\helpers\Database;
use app\helpers\Logging;
use app\helpers\Mailing;
use app\helpers\Util;

App::setConfig(LogChannel::SERVER, false, true);

// iterate through users and articles to flag articles that are soon due.
$articles = Database::articles()->queryAll();
$users = Database::users()->queryAll();

Logging::info('Starting server script.');

foreach ($users as $user) {
    echo PHP_EOL . PHP_EOL;

    $delays = $user->getContactDelays();
    $reminders = [];

    foreach ($articles as $article) {

        $delta_days = Util::getDaysUntil($article->getExpirationDate());
        if (!$delta_days) {
            continue;
        }

        foreach ($delays as $delay) {
            //send reminder only once when the delay exactly matches the remaining days before peremption.
            if ($delta_days === $delay) {

                array_push($reminders, [
                    'article' => $article,
                    'delay' => $delay
                ]);
            }
        }
    }

    if (!Mailing::peremptionNotification($user, $reminders)) {
        Logging::error('peremption notification failed.');
        return;
    }
}
