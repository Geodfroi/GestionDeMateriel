<?php

declare(strict_types=1);

use app\constants\Session;
use app\helpers\BaseRoute;
use app\helpers\Database;
use app\helpers\Mailing;
use app\helpers\Util;
use app\models\User;

################################
## Joël Piguet - 2022.04.29 ###
##############################
# 

require_once __DIR__ . '/../loader.php';
require_once __DIR__ . '/../vendor/autoload.php'; // use composer to load autofile.
session_start();
$_SESSION[Session::ROOT] = APP_URL;

const LAST_CHECKED_FILE = "last-checked.txt";

/**
 * Check if logic has already run today. Avoid sending emails twice if the route is refreshed in the browser.
 * Put last check date in check file.
 */
function checkSentToday(): bool
{
    $str = file_get_contents(LAST_CHECKED_FILE);
    $today = (new DateTime())->format('Y-m-d');
    if ($str && $str === $today) {
        return true;
    }
    $check_file = fopen(LAST_CHECKED_FILE, "w");
    fwrite($check_file, $today);
    return false;
}

class UserEmails
{
    private $emails = [];
    private $user;
    private $was_sent = false;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function getEmails(): array
    {
        return $this->emails;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function hasEmails(): bool
    {
        return count($this->emails) > 0;
    }

    public function wasSent(): bool
    {
        return $this->was_sent;
    }

    public function send_reminders()
    {
        $this->was_sent = Mailing::peremptionNotification($this->user, $this->emails);
    }

    public function setEmail($article, $delay)
    {
        array_push($this->emails, [
            'article' => $article,
            'delay' => $delay,
        ]);
    }
}

/**
 * Iterate through articles and send flag emails when they are close to peremption set by user.
 * 
 * @return array array of UserEmails.
 */
function fetchPeremptionReminders(): array
{
    $reminders = [];
    $articles = Database::articles()->queryAll();
    $users = Database::users()->queryAll();

    foreach ($users as $user) {
        $user_email = new UserEmails($user);
        array_push($reminders, $user_email);

        $delays = $user->getContactDelays();
        foreach ($articles as $article) {

            $delta_days = Util::getDaysUntil($article->getExpirationDate());
            if (!$delta_days) {
                continue;
            }

            foreach ($delays as $delay) {
                //send reminder only once when the delay exactly matches the remaining days before peremption.
                if ($delta_days === $delay) {
                    $user_email->setEmail($article, $delay);
                }
            }
        }
    }

    return $reminders;
}

/**
 * Route accessed to backup db and distribute remainder emails.
 */
class Server extends BaseRoute
{
    public function __construct()
    {
        parent::__construct('server', 'server-template', "", false, false);
    }

    public function getBodyContent(): string
    {
        $already_checked_today = checkSentToday();
        $reminders = null;

        if (!$already_checked_today) {
            $reminders = fetchPeremptionReminders();
            foreach ($reminders as $user_email) {
                if ($user_email->hasEmails()) {
                    $user_email->send_reminders();
                }
            }
        }

        return $this->renderTemplate([
            'already_checked' => $already_checked_today,
            // 'backup_success' =>  Database::backup(),
            'reminders' => $reminders,
        ]);
    }
}

echo (new Server())->renderRoute();
