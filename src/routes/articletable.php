<?php

################################
## Joël Piguet - 2021.12.09 ###
##############################

namespace app\routes;

use app\constants\Alert;
use app\constants\OrderBy;
use app\constants\Route;
use app\constants\Session;
use app\constants\Settings;
use app\helpers\Authenticate;
use app\helpers\Database;
use app\helpers\Util;
use app\models\Article;

/**
 * Route class containing behavior linked to user_template. This route display an user Article list and allows create-remove-update tasks on articles list.
 */
class ArticleTable extends BaseRoute
{
    public function __construct()
    {
        parent::__construct('articles_table_template', Route::ART_TABLE);
    }

    public function getBodyContent(): string
    {
        if (!Authenticate::isLoggedIn()) {
            $this->requestRedirect(Route::LOGIN);
        }

        $_SESSION[Session::ART_PAGE] ??= 1;
        $_SESSION[Session::ART_ORDERBY] ??= OrderBy::DELAY_ASC;
        $_SESSION[Session::ART_FILTER_TYPE] ??= 0;
        $_SESSION[Session::ART_FILTER_VAL] ??= '';

        if (isset($_GET['alert'])) {

            if ($_GET['alert'] === 'added_success') {
                $this->setAlert(AlertType::SUCCESS, Alert::ARTICLE_ADD_SUCCESS);
            } else if ($_GET['alert'] === 'added_failure') {
                $this->setAlert(AlertType::FAILURE, Alert::ARTICLE_ADD_FAILURE);
            } else if ($_GET['alert'] === 'updated_success') {
                $this->setAlert(AlertType::SUCCESS, Alert::ARTICLE_UPDATE_SUCCESS);
            } else if ($_GET['alert'] === 'updated_failure') {
                $this->setAlert(AlertType::FAILURE, Alert::ARTICLE_UPDATE_FAILURE);
            }
            goto end;
        }

        if (isset($_GET['delete'])) {
            if (Database::articles()->delete($_GET['delete'])) {
                $this->setAlert(AlertType::SUCCESS, Alert::ARTICLE_REMOVE_SUCCESS);
            } else {
                $this->setAlert(AlertType::FAILURE, Alert::ARTICLE_REMOVE_FAILURE);
            }
            goto end;
        }

        if (isset($_GET['orderby'])) {
            $_SESSION[Session::ART_ORDERBY] = intval($_GET['orderby']);
            goto end;
        }

        if (isset($_GET['page'])) {
            $_SESSION[Session::ART_PAGE] = intval($_GET['page']);
            goto end;
        }

        if (isset($_POST['filter'])) {
            $_SESSION[Session::ART_FILTER_TYPE]  = $_POST['filter-type'];
            $_SESSION[Session::ART_FILTER_VAL]  = $_POST['filter-val'];
            goto end;
        }

        end:

        $item_count = Database::articles()->queryCount($_SESSION[Session::ART_FILTER_TYPE], $_SESSION[Session::ART_FILTER_VAL]);

        $offset =   ($_SESSION[Session::ART_PAGE] - 1) * Settings::TABLE_DISPLAY_COUNT;
        $page_count = ceil($item_count / Settings::TABLE_DISPLAY_COUNT);

        $articles = Database::articles()->queryAll(
            Settings::TABLE_DISPLAY_COUNT,
            $offset,
            $_SESSION[Session::ART_ORDERBY],
            $_SESSION[Session::ART_FILTER_TYPE],
            $_SESSION[Session::ART_FILTER_VAL]
        );

        // usort($articles, array($this, "_compareExpired"));

        return $this->renderTemplate([
            'articles' =>  $articles,
            'page_count' => $page_count,
        ]);
    }

    // /**
    //  * sort results so that articles past the expiration date are pushed at the bottom.
    //  */
    // function _compareExpired(Article $a, Article $b): int
    // {
    //     $delta_a = Util::getDaysUntil($a->getExpirationDate());
    //     $delta_b = Util::getDaysUntil($b->getExpirationDate());
    //     return   $delta_a >    $delta_b;

    //     // if ($delta_a > 0 && $delta_b < 0) {
    //     //     return 1;
    //     // }
    //     // if ($delta_a < 0 && $delta_b > 0) {
    //     //     return -1;
    //     // }
    //     // return 0;
    // }
}
