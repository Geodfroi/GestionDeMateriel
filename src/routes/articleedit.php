<?php

################################
## Joël Piguet - 2022.01.17 ###
##############################

namespace app\routes;

use app\constants\Route;
use app\helpers\Database;


class ArticleEdit extends BaseRoute
{
    function __construct()
    {
        parent::__construct(Route::ART_EDIT, 'article_edit_template', 'article_edit');
    }

    public function getBodyContent(): string
    {
        // An invisible field in the form will hold the id value if the form is used to update an existing Article.

        if (isset($_GET['update'])) {
            $article = Database::articles()->queryById($_GET['update']);
            $article_id = $article->getId();
        }

        return $this->renderTemplate(['id' => $article_id ?? '']);
    }
}
