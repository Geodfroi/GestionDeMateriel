<?php

################################
## Joël Piguet - 2021.12.14 ###
##############################

namespace app\routes;

use Exception;

use app\constants\Route;
use app\helpers\Mailing;
use app\models\Article;

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
