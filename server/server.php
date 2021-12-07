<?php

declare(strict_types=1);

################################
## Joël Piguet - 2021.12.06 ###
##############################
// periodically iterate through articles and send reminder emails when they are close to peremption

require_once __DIR__ . '/../src/const.php';
require_once __DIR__ . '/../p_settings.php';
require_once __DIR__ . '/../vendor/autoload.php'; // use composer to load autofile.

use app\helpers\Database;
use app\helpers\Mailing;

$articles = Database::articles()->queryAll();
$users = Database::users()->queryAll();
$today = new DateTime();

// iterate through users and articles to flag articles that are soon due.
foreach ($users as $user) {
    echo PHP_EOL . PHP_EOL;

    $delays = $user->getContactDelays();


    $reminders = [];

    foreach ($articles as $article) {
        //check peremption
        $interval = $today->diff($article->getExpirationDate());
        if ($interval->invert) {
            // interval is negative: expiration date is already past.
            continue;
        }
        $delta_days = intval($interval->format('%a'));
        // echo 'delta: ' . $delta_days . PHP_EOL;

        foreach ($delays as $delay) {
            //send reminder only once when the delay exactly matches the remaining days before peremption.
            if ($delta_days === $delay) {
                array_push($reminders, [
                    'article' => $article,
                    'delay' => $delay
                ]);
                // echo 'ok' . PHP_EOL;
            } else {
            }
        }
    }

    if (!Mailing::peremptionNotification($recipient, $emails, $reminders)) {
        error_log('peremption notification failed');
        return;
    }

    // echo Mailing::peremptionNotificationBody($recipient, $reminders);
}
