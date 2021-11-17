<?php

################################
## Joël Piguet - 2021.11.17 ###
##############################

namespace routes;

use helpers\Authenticate;
use helpers\Database;

/**
 * Route class containing behavior linked to user_template. This route display an user Article list and allows create-remove-update tasks on articles list.
 */
class ArticlesRoute extends BaseRoute
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

        $articles = [];
        $user = Authenticate::getUser();

        if (isset($user)) {
            $articles = Database::getInstance()->getUserArticles($user->getId(), ArticlesRoute::DISPLAY_COUNT, ArticlesRoute::DEBUG_OFFSET);
        }

        return $this->renderTemplate([
            'articles' =>  $articles
        ]);
    }
}
