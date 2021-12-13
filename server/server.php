<?php

declare(strict_types=1);

################################
## Joël Piguet - 2021.12.12 ###
##############################
// periodically iterate through articles and send reminder emails when they are close to peremption

require_once __DIR__ . '/../vendor/autoload.php'; // use composer to load autofile.

use app\constants\LogChannel;
use app\helpers\Database;
use app\helpers\Logging;
use app\helpers\Mailing;
use app\helpers\Util;

$articles = Database::articles()->queryAll();
$users = Database::users()->queryAll();

Logging::info('Starting server script.', [], LogChannel::SERVER);

// iterate through users and articles to flag articles that are soon due.
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

    if (!Mailing::peremptionNotification($user, $reminders, LogChannel::SERVER)) {
        Logging::error('peremption notification failed.', [], LogChannel::SERVER);
        return;
    }
}
