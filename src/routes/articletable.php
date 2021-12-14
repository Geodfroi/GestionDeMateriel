<?php

################################
## Joël Piguet - 2021.12.13 ###
##############################

namespace app\routes;

use app\constants\Alert;
use app\constants\AlertType;
use app\constants\ArtFilter;
use app\constants\LogInfo;
use app\constants\OrderBy;
use app\constants\Route;
use app\constants\Session;
use app\constants\Settings;
use app\helpers\Authenticate;
use app\helpers\Database;
use app\helpers\Logging;

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

        $user_id = Authenticate::getUserId();

        $_SESSION[Session::ART_PAGE] ??= 1;
        $_SESSION[Session::ART_ORDERBY] ??= OrderBy::DELAY_ASC;

        if (!isset($_SESSION[Session::ART_FILTERS])) {
            $_SESSION[Session::ART_FILTERS] = [];
        }

        if (isset($_GET['alert'])) {

            if ($_GET['alert'] === 'added_success') {
                $this->showAlert(AlertType::SUCCESS, Alert::ARTICLE_ADD_SUCCESS);
            } else if ($_GET['alert'] === 'added_failure') {
                $this->showAlert(AlertType::FAILURE, Alert::ARTICLE_ADD_FAILURE);
            } else if ($_GET['alert'] === 'updated_success') {
                $this->showAlert(AlertType::SUCCESS, Alert::ARTICLE_UPDATE_SUCCESS);
            } else if ($_GET['alert'] === 'updated_failure') {
                $this->showAlert(AlertType::FAILURE, Alert::ARTICLE_UPDATE_FAILURE);
            }
            goto end;
        }

        if (isset($_GET['delete'])) {
            if (Database::articles()->delete($_GET['delete'])) {
                $this->showAlert(AlertType::SUCCESS, Alert::ARTICLE_REMOVE_SUCCESS);
                Logging::info(LogInfo::ARTICLE_DELETED, ['user-id' => $user_id, 'article-id' => $_GET['delete']]);
            } else {
                $this->showAlert(AlertType::FAILURE, Alert::ARTICLE_REMOVE_FAILURE);
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
            if ($_POST['filter-name']) {
                $_SESSION[Session::ART_FILTERS][ArtFilter::NAME] = $_POST['filter-name'];
            } else {
                // if null of empty unset from filter array.
                unset($_SESSION[Session::ART_FILTERS][ArtFilter::NAME]);
            }

            if ($_POST['filter-location']) {
                $_SESSION[Session::ART_FILTERS][ArtFilter::LOCATION] = $_POST['filter-location'];
            } else {
                unset($_SESSION[Session::ART_FILTERS][ArtFilter::LOCATION]);
            }

            // remove date filters from associative array
            unset($_SESSION[Session::ART_FILTERS][ArtFilter::DATE_BEFORE]);
            unset($_SESSION[Session::ART_FILTERS][ArtFilter::DATE_AFTER]);

            $date_val = isset($_POST['filter-date-val']) ? $_POST['filter-date-val'] : '';

            // add date filter from input selection with correct type as key

            if ($_POST['filter-date-type'] === ArtFilter::DATE_BEFORE) {
                $_SESSION[Session::ART_FILTERS][ArtFilter::DATE_BEFORE] = $date_val;
            } elseif ($_POST['filter-date-type'] === ArtFilter::DATE_AFTER) {
                $_SESSION[Session::ART_FILTERS][ArtFilter::DATE_AFTER] = $date_val;
            }

            if (isset($_POST['show-expired'])) {
                $_SESSION[Session::ART_FILTERS][ArtFilter::SHOW_EXPIRED] = true;
            } else {
                unset($_SESSION[Session::ART_FILTERS][ArtFilter::SHOW_EXPIRED]);
            }

            goto end;
        }

        end:

        $item_count = Database::articles()->queryCount($_SESSION[Session::ART_FILTERS]);
        $offset = ($_SESSION[Session::ART_PAGE] - 1) * Settings::TABLE_DISPLAY_COUNT;
        $page_count = ceil($item_count / Settings::TABLE_DISPLAY_COUNT);

        $articles = Database::articles()->queryAll(
            Settings::TABLE_DISPLAY_COUNT,
            $offset,
            $_SESSION[Session::ART_ORDERBY],
            $_SESSION[Session::ART_FILTERS]
        );

        return $this->renderTemplate([
            'articles' =>  $articles,
            'page_count' => $page_count,
        ]);
    }
}
