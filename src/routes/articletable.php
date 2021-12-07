<?php

################################
## Joël Piguet - 2021.12.07 ###
##############################

namespace app\routes;

use app\helpers\Authenticate;
use app\helpers\Database;

/**
 * Route class containing behavior linked to user_template. This route display an user Article list and allows create-remove-update tasks on articles list.
 */
class ArticleTable extends BaseRoute
{
    public function __construct()
    {
        parent::__construct(ART_TABLE_TEMPLATE, ART_TABLE);
    }

    public function getBodyContent(): string
    {
        if (!Authenticate::isLoggedIn()) {
            $this->requestRedirect(LOGIN);
        }

        $_SESSION[ART_PAGE] ??= 1;
        $_SESSION[ART_ORDERBY] ??= DATE_DESC;

        if (isset($_GET['alert'])) {

            if ($_GET['alert'] === 'added_success') {
                $this->setAlert(AlertType::SUCCESS, ARTICLE_ADD_SUCCESS);
            } else if ($_GET['alert'] === 'added_failure') {
                $this->setAlert(AlertType::FAILURE, ARTICLE_ADD_FAILURE);
            } else if ($_GET['alert'] === 'updated_success') {
                $this->setAlert(AlertType::SUCCESS, ARTICLE_UPDATE_SUCCESS);
            } else if ($_GET['alert'] === 'updated_failure') {
                $this->setAlert(AlertType::FAILURE, ARTICLE_UPDATE_FAILURE);
            }
            goto end;
        }

        if (isset($_GET['delete'])) {
            if (Database::articles()->delete($_GET['delete'])) {
                $this->setAlert(AlertType::SUCCESS, ARTICLE_REMOVE_SUCCESS);
            } else {
                $this->setAlert(AlertType::FAILURE, ARTICLE_REMOVE_FAILURE);
            }
            goto end;
        }

        if (isset($_GET['orderby'])) {
            $_SESSION[ART_ORDERBY] = intval($_GET['orderby']);
            goto end;
        }

        if (isset($_GET['page'])) {
            $_SESSION[ART_PAGE] = intval($_GET['page']);
            goto end;
        }

        if (isset($_POST['filter'])) {
            var_dump($_POST);
            goto end;
        }

        end:

        $item_count = Database::articles()->queryCount();
        $offset =   ($_SESSION[ART_PAGE] - 1) * TABLE_DISPLAY_COUNT;
        $page_count = ceil($item_count / TABLE_DISPLAY_COUNT);

        $articles = Database::articles()->queryAll(TABLE_DISPLAY_COUNT, $offset, $_SESSION[ART_ORDERBY]);

        return $this->renderTemplate([
            'articles' =>  $articles,
            'page_count' => $page_count,
        ]);
    }
}
