<?php

################################
## Joël Piguet - 2022.01.19 ###
##############################

namespace app\routes;

use app\constants\Route;
use app\helpers\Database;
// use app\helpers\Logging;

class ArticleEdit extends BaseRoute
{
    function __construct()
    {
        parent::__construct(Route::ART_EDIT, 'article_edit_template', 'article_edit_script');
    }

    public function getBodyContent(): string
    {
        $mode = 'add';
        if (isset($_GET['update'])) {
            $article = Database::articles()->queryById($_GET['update']);
            $mode = 'update';
        }

        return $this->renderTemplate([
            'article' => $mode == 'update' ? $article->asArray() : '',
            'mode' =>  $mode,
            'loc_presets' =>  Database::locations()->queryAll(),
        ]);
    }
}
