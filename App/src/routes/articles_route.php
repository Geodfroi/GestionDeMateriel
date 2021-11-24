<?php

################################
## Joël Piguet - 2021.11.24 ###
##############################

namespace routes;

use Error;
use helpers\Authenticate;
use helpers\Database;
use helpers\ArtOrder;

/**
 * Route class containing behavior linked to user_template. This route display an user Article list and allows create-remove-update tasks on articles list.
 */
class ArticlesList extends BaseRoute
{
    const ADD_SUCCESS = "L'article a été correctement enregistré.";
    const ADD_FAILURE = "L'article n'a pas pu être enregistré. Veuillez réessayer.";
    const REMOVE_SUCCESS = "L'article a été enlevé avec succès";
    const REMOVE_FAILURE = "L'article n'a pas pu être effacé.";
    const UPDATE_SUCCESS = "L'article a été mis à jour avec succès";
    const UPDATE_FAILURE = "L'article n'a pas pu être mis à jour.";

    const DISPLAY_COUNT = 12;

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
        $orderby = ArtOrder::DATE_DESC;
        $page = 1;

        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            if (isset($_GET['alert'])) {
                if ($_GET['alert'] === 'added_success') {
                    $alerts['success'] = ArticlesList::ADD_SUCCESS;
                } else if ($_GET['alert'] === 'added_failure') {
                    $alerts['failure'] = ArticlesList::ADD_FAILURE;
                } else if ($_GET['alert'] === 'updated_success') {
                    $alerts['success'] = ArticlesList::UPDATE_SUCCESS;
                } else if ($_GET['alert'] === 'updated_failure') {
                    $alerts['failure'] = ArticlesList::UPDATE_FAILURE;
                }

                if (isset($_GET['delete'])) {
                    if (Database::getInstance()->deleteArticleByID($_GET['delete'])) {
                        $alerts['success'] = ArticlesList::REMOVE_SUCCESS;
                    } else {
                        $alerts['failure'] = ArticlesList::REMOVE_FAILURE;
                    }
                }
            } else if (isset($_GET['orderby'])) {
                $orderby = intval($_GET['orderby']);
                $page = intval($_GET['page']);
            }
        }

        $item_count = Database::getInstance()->getUserArticlesCount($user->getId());
        $offset =   ($page - 1) * ArticlesList::DISPLAY_COUNT;
        $page_count = ceil($item_count / ArticlesList::DISPLAY_COUNT);

        if (isset($user)) {
            $articles = Database::getInstance()->getUserArticles($user->getId(), ArticlesList::DISPLAY_COUNT, $offset, $orderby);
        }

        return $this->renderTemplate([
            'articles' =>  $articles,
            'alerts' => $alerts,
            'orderby' => $orderby,
            'page' => $page,
            'page_count' => $page_count,
        ]);
    }
}
