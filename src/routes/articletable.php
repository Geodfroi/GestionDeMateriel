<?php

################################
## Joël Piguet - 2022.03.14 ###
##############################

namespace app\routes;

use app\constants\ArtFilter;
use app\constants\OrderBy;
use app\constants\Route;
use app\constants\Session;
use app\helpers\BaseRoute;
use app\helpers\Database;
use app\helpers\Logging;

/**
 * Route class containing behavior linked to user_template. This route display an user Article list.
 */
class ArticleTable extends BaseRoute
{
    public function __construct()
    {
        parent::__construct(Route::ART_TABLE, 'articles_table_template', 'articles_table_script');
    }

    public function getBodyContent(): string
    {
        $display_data = isset($_SESSION[Session::ARTICLES_DISPLAY]) ? json_decode($_SESSION[Session::ARTICLES_DISPLAY], true) : [];
        // logging::debug('display_data', $display_data);
        if (!isset($display_data['page'])) {
            $display_data['page'] = 1;
        }
        if (!isset($display_data['display_count'])) {
            $display_data['display_count'] =
                TABLE_DISPLAY_COUNT;
        }
        if (!isset($display_data['filters'])) {
            $display_data['filters'] = [];
        }
        if (!isset($display_data['orderby'])) {
            $display_data['orderby'] = OrderBy::DELAY_ASC;
        }

        if (isset($_GET['display_count'])) {
            $display_data['display_count'] = intval($_GET['display_count']);
            goto end;
        }

        if (isset($_GET['orderby'])) {
            $display_data['orderby'] = $_GET['orderby'];
            goto end;
        }

        if (isset($_GET['page'])) {
            $display_data['page'] = intval($_GET['page']);
            goto end;
        }

        if (isset($_GET['filter'])) {

            // logging::debug('filter', $_GET);
            $display_data['filters'] = [];

            if ($_GET['filter'] === 'clearAll') {
                $_SESSION[Session::ARTICLES_DISPLAY] = json_encode($display_data);
                return $this->requestRedirect(Route::ART_TABLE);
            }

            if ($_GET['filter-name']) {
                $display_data['filters'][ArtFilter::NAME] = $_GET['filter-name'];
            } else {
                // if null of empty unset from filter array.
                unset($display_data['filters'][ArtFilter::NAME]);
            }

            if ($_GET['filter-location']) {
                $display_data['filters'][ArtFilter::LOCATION] = $_GET['filter-location'];
            } else {
                unset($display_data['filters'][ArtFilter::LOCATION]);
            }

            if (isset($_GET['filter-date-val'])) {
                $display_data['filters'][ArtFilter::DATE_VALUE] =
                    $_GET['filter-date-val'];
            } else {
                unset($display_data['filters'][ArtFilter::DATE_VALUE]);
            }

            $display_data['filters'][ArtFilter::DATE_TYPE] = $_GET['filter-date-type'];

            if (isset($_GET['filter-show-expired'])) {
                $display_data['filters'][ArtFilter::SHOW_EXPIRED] = true;
            } else {
                unset($display_data['filters'][ArtFilter::SHOW_EXPIRED]);
            }
        }

        end:

        // logging::debug('display_data2', $display_data);
        $_SESSION[Session::ARTICLES_DISPLAY] = json_encode($display_data);

        $display_count = $display_data['display_count'] ?? 20;
        $item_count = Database::articles()->queryCount($display_data['filters']);
        $offset = ($display_data['page'] - 1) * $display_count;
        $page_count = ceil($item_count / $display_count);

        $articles = Database::articles()->queryAll(
            $display_count,
            $offset,
            $display_data['orderby'],
            $display_data['filters']
        );

        Logging::debug('display_data: ', $display_data);
        $display_data['page_count'] = $page_count;
        return $this->renderTemplate([
            'articles' =>  $articles,
            'display_data' => $display_data,
        ]);
    }
}
