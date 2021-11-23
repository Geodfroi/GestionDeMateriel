<?php

################################
## Joël Piguet - 2021.11.23 ###
##############################

namespace routes;

use helpers\Authenticate;
use helpers\Database;

/**
 * Route class containing behavior linked to user_template. This route display an user Article list and allows create-remove-update tasks on articles list.
 */
class ArticlesList extends BaseRoute
{
    const DISPLAY_COUNT = 12;
    const DEBUG_OFFSET = 0;

    public function __construct()
    {
        parent::__construct("articles_template");
    }

    public function getBodyContent(): string
    {
        if (!Authenticate::isLoggedIn()) {
            $this->requestRedirect(Routes::LOGIN);
        }

        $alerts = [];
        $articles = [];
        $user = Authenticate::getUser();

        if (isset($user)) {
            $articles = Database::getInstance()->getUserArticles($user->getId(), ArticlesList::DISPLAY_COUNT, ArticlesList::DEBUG_OFFSET);
        }

        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            if (isset($_GET['alert'])) {
                $alerts['added-alert'] = $_GET['alert'];
            }

            if (isset($_GET['delete'])) {
                if (Database::getInstance()->deleteArticleByID($_GET['delete'])) {
                    $alerts['removed-alert'] = 'removed_success';
                } else {
                    $alerts['removed-alert'] = 'removed_failure';
                }
            }
        }

        return $this->renderTemplate([
            'articles' =>  $articles,
            'alerts' => $alerts,
        ]);
    }
}
