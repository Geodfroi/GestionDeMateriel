<?php

declare(strict_types=1);

################################
## Joël Piguet - 2022.04.06 ###
##############################

use app\helpers\BaseRoute;
use app\helpers\Logging;
use app\helpers\Mailing;
use app\models\Article;

require_once __DIR__ . '/../loader.php';
require_once __DIR__ . '/../vendor/autoload.php'; // use composer to load autofile.
session_start();


class DebugEmails extends BaseRoute
{
    function __construct()
    {
        parent::__construct('debugmails', 'debugemails_template', "", false, false);
    }

    public function getBodyContent(): string
    {
        if (isset($_GET['show'])) {

            switch ($_GET['show']) {
                case 'newpassword':
                    [$html_template, $plaintext] = Mailing::passwordChangeNotificationBody('mathias.r@gmail.com', 'HEDS2000');
                    break;
                case 'reminder':
                    $array = [];
                    array_push($array, [
                        'article' => Article::fromForm(0, 'Pneu', 'Garage', '2021-12-19'),
                        'delay' => 15,
                    ]);
                    array_push($array, [
                        'article' => Article::fromForm(1, 'Bouquet', 'Fleuriste', '2021-12-15'),
                        'delay' => 3,
                    ]);
                    array_push($array, [
                        'article' => Article::fromForm(2, 'Seringues stériles x20', 'Armoire à pharmacie 1er étage', '2022-01-15'),
                        'delay' => 7,
                    ]);

                    [$html_template, $plaintext] = Mailing::peremptionNotificationBody('noel.biquet@gmail.com', $array);
                    break;
                case 'userinvite':
                    [$html_template, $plaintext] = Mailing::userInviteNotificationBody('noel.biquet@gmail.com', 'HEDS3000');
                    break;
                default:
                    throw new Exception('Invalid parameter');
            }
        }

        return $this->renderTemplate([
            'html_template' =>  $html_template ?? '',
            'plaintext' =>  $plaintext ?? '',
        ], false);
    }
}


Logging::debug("debugmails route");
echo (new DebugEmails())->renderRoute();
