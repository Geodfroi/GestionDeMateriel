<?php

################################
## Joël Piguet - 2022.01.17 ###
##############################

namespace app\routes;

use app\constants\ArtFilter;
use app\constants\OrderBy;
use app\constants\Route;
use app\constants\Session;
use app\helpers\Authenticate;
use app\helpers\Database;
use app\helpers\Util;

// use app\helpers\Logging;

/**
 * Route class containing behavior linked to user_template. This route display an user Article list and allows create-remove-update tasks on articles list.
 */
class ArticleTable extends BaseRoute
{
    public function __construct()
    {
        parent::__construct(Route::ART_TABLE, 'articles_table_template', 'articles_table_script');
    }

    public function getBodyContent(): string
    {
        if (!Authenticate::isLoggedIn()) {
            $this->requestRedirect(Route::LOGIN);
        }

        $_SESSION[Session::ART_PAGE] ??= 1;
        $_SESSION[Session::ART_ORDERBY] ??= OrderBy::DELAY_ASC;

        if (!isset($_SESSION[Session::ART_FILTERS])) {
            $_SESSION[Session::ART_FILTERS] = [];
        }

        if (isset($_GET['display'])) {
            $_SESSION[Session::ART_DISPLAY_COUNT] = intval($_GET['display']);
            goto end;
        }

        if (isset($_GET['orderby'])) {
            $_SESSION[Session::ART_ORDERBY] = $_GET['orderby'];
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

        $display_count = $_SESSION[Session::ART_DISPLAY_COUNT] ?? 20;
        $item_count = Database::articles()->queryCount($_SESSION[Session::ART_FILTERS]);
        $offset = ($_SESSION[Session::ART_PAGE] - 1) * $display_count;
        $page_count = ceil($item_count / $display_count);

        $articles = Database::articles()->queryAll(
            $display_count,
            $offset,
            $_SESSION[Session::ART_ORDERBY],
            $_SESSION[Session::ART_FILTERS]
        );

        return $this->renderTemplate([
            'articles' =>  $articles,
            'page_count' => $page_count,
            'display_count' =>  $display_count,
            'filter_str' => json_encode($_SESSION[Session::ART_FILTERS]) // Util::associativeToString($_SESSION[Session::ART_FILTERS]),
        ]);
    }
}
