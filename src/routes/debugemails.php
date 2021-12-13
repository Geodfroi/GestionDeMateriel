<?php

################################
## Joël Piguet - 2021.12.13 ###
##############################

namespace app\routes;

use app\constants\Alert;
use app\constants\AlertType;
use app\constants\LogChannel;
use app\constants\LogInfo;
use app\constants\Route;
use app\constants\Warning;
use app\helpers\Authenticate;
use app\helpers\Database;
use app\helpers\Logging;
use app\helpers\Mailing;
use app\helpers\Validation;
use app\models\Article;
use Exception;

class DebugEmails extends BaseRoute
{
    function __construct()
    {
        parent::__construct('debug_emails_template', Route::DEBUG_EMAILS, false, false);
    }

    public function getBodyContent(): string
    {
        if (isset($_GET['show'])) {

            switch ($_GET['show']) {
                case 'newpassword':
                    $email_template = Mailing::passwordChangeNotificationBody('Mathias', 'HEDS2000');
                    break;
                case 'reminder':
                    $array = [
                        Article::fromForm(0, 'Pneu', 'Garage', '2021-12-19') => 15,
                        Article::fromForm(1, 'Bouquet', 'Fleuriste', '2021-12-15')  => 3,
                        Article::fromForm(2, 'Seringues stériles x20', 'Armoire à pharmacie 1er étage', '2022-01-15')  => 7,
                    ];
                    $email_template = Mailing::peremptionNotificationBody('José', $array);
                    break;
                case 'userinvite':
                    $email_template = Mailing::userInviteNotificationBody('noël.biquet@gmail.com', 'HEDS3000');
                    break;
                default:
                    throw new Exception('Invalid parameter');
            }
        }

        return $this->renderTemplate([
            'email_template' =>  $email_template ?? '',
        ]);
    }
}
