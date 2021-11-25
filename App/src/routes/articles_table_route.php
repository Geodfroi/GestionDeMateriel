<?php

################################
## Joël Piguet - 2021.11.25 ###
##############################

namespace routes;

use helpers\ArtOrder;
use helpers\Authenticate;
use helpers\Database;


/**
 * Route class containing behavior linked to user_template. This route display an user Article list and allows create-remove-update tasks on articles list.
 */
class ArticlesTable extends BaseRoute
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
        parent::__construct("articles_table_template", ART_TABLE);
    }

    public function getBodyContent(): string
    {
        if (!Authenticate::isLoggedIn()) {
            $this->requestRedirect(LOGIN);
        }

        $_SESSION[ART_PAGE] ??= 1;
        $_SESSION[ART_ORDER_BY] ??= ArtOrder::DATE_DESC;

        $alerts = [];

        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            if (isset($_GET['alert'])) {
                if ($_GET['alert'] === 'added_success') {
                    $alerts['success'] = ArticlesTable::ADD_SUCCESS;
                } else if ($_GET['alert'] === 'added_failure') {
                    $alerts['failure'] = ArticlesTable::ADD_FAILURE;
                } else if ($_GET['alert'] === 'updated_success') {
                    $alerts['success'] = ArticlesTable::UPDATE_SUCCESS;
                } else if ($_GET['alert'] === 'updated_failure') {
                    $alerts['failure'] = ArticlesTable::UPDATE_FAILURE;
                }

                if (isset($_GET['delete'])) {
                    if (Database::getInstance()->deleteArticleByID($_GET['delete'])) {
                        $alerts['success'] = ArticlesTable::REMOVE_SUCCESS;
                    } else {
                        $alerts['failure'] = ArticlesTable::REMOVE_FAILURE;
                    }
                }
            } else if (isset($_GET['orderby'])) {
                $_SESSION[ART_ORDER_BY] = intval($_GET['orderby']);
            } else if (isset($_GET['page'])) {
                $_SESSION[ART_PAGE] = intval($_GET['page']);
            }
        }

        $user = Authenticate::getUser();
        $item_count = Database::getInstance()->getUserArticlesCount($user->getId());
        $offset =   ($_SESSION[ART_PAGE] - 1) * ArticlesTable::DISPLAY_COUNT;
        $page_count = ceil($item_count / ArticlesTable::DISPLAY_COUNT);

        $articles = [];
        if (isset($user)) {
            $articles = Database::getInstance()->getUserArticles($user->getId(), ArticlesTable::DISPLAY_COUNT, $offset, $_SESSION[ART_ORDER_BY]);
        }

        return $this->renderTemplate([
            'articles' =>  $articles,
            'alerts' => $alerts,
            'page_count' => $page_count,
        ]);
    }
}
