<?php

declare(strict_types=1);

################################
## Joël Piguet - 2022.04.05 ###
##############################

use app\constants\Alert;
use app\constants\AlertType;
use app\constants\LogInfo;
use app\constants\Route;
use app\helpers\Authenticate;
use app\helpers\BaseRoute;
use app\helpers\Database;
use app\helpers\Logging;
use app\helpers\RequestUtil;
use app\helpers\Util;
use app\models\Article;

require_once __DIR__ . '/../loader.php';
require_once __DIR__ . '/../vendor/autoload.php'; // use composer to load autofile.
session_start();

class ArticleEdit extends BaseRoute
{
    function __construct()
    {
        parent::__construct('articleedit', 'articleedit_template', 'articleedit_script');
    }

    public function getBodyContent(): string
    {
        $mode = 'add';
        if (isset($_GET['update'])) {
            $id_ = intval($_GET['update']);
            $article = Database::articles()->queryById($id_);
            $mode = 'update';
        }

        return $this->renderTemplate([
            'article' => $mode == 'update' ? $article->asArray() : '',
            'mode' =>  $mode,
            'loc_presets' =>  Database::locations()->queryAll(),
        ]);
    }
}

/**
 * @return string json response.
 */
function addArticle($json): string
{
    $article_name = isset($json['article-name']) ? $json['article-name'] : "";
    $location = isset($json['location']) ? $json['location'] : "";
    $exp_date_str = isset($json['expiration-date']) ? $json['expiration-date'] : "";
    $comments = isset($json['comments']) ? $json['comments'] : "";
    $warnings = [];

    if (validateArticleInputs($article_name, $location, $exp_date_str, $comments, $warnings)) {
        $user_id = Authenticate::getUserId();
        $article = Article::fromForm($user_id, $article_name, $location, $exp_date_str, $comments);

        $article_id = Database::articles()->insert($article);
        if ($article_id) {
            Logging::info(LogInfo::ARTICLE_CREATED, ['user-id' => $user_id, 'article-id' => $article_id]);
            return RequestUtil::redirectJSON(Route::ART_TABLE, AlertType::SUCCESS, ALERT::ARTICLE_ADD_SUCCESS);
        }
        return RequestUtil::redirectJSON(Route::ART_TABLE, AlertType::FAILURE, ALERT::ARTICLE_ADD_FAILURE);
    }
    return RequestUtil::issueWarnings($json, $warnings);
}

/**
 * @return string json response.
 */
function updateArticle($json): string
{
    $article_id  = intval($json['id']);
    $article_name = isset($json['article-name']) ? $json['article-name'] : "";
    $location = isset($json['location']) ? $json['location'] : "";
    $exp_date_str = isset($json['expiration-date']) ? $json['expiration-date'] : "";
    $comments = isset($json['comments']) ? $json['comments'] : "";
    $warnings = [];

    if (validateArticleInputs($article_name, $location, $exp_date_str, $comments, $warnings)) {
        $user_id = Authenticate::getUserId();
        $article = Database::articles()->queryById($article_id);
        $article->updateFields($article_name, $location, $exp_date_str, $comments);

        if (Database::articles()->update($article)) {
            Logging::info(LogInfo::ARTICLE_UPDATED, ['user-id' => $user_id, 'article-id' => $article_id]);
            return RequestUtil::redirectJSON(Route::ART_TABLE, AlertType::SUCCESS, ALERT::ARTICLE_UPDATE_SUCCESS);
        }
        return RequestUtil::redirectJSON(Route::ART_TABLE, AlertType::FAILURE, ALERT::ARTICLE_UPDATE_FAILURE);
    }
    Logging::debug('warnings-update', ['warnings' => $warnings]);
    return RequestUtil::issueWarnings($json, $warnings);
}

Logging::debug("articleedit route");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $json = RequestUtil::retrievePOSTData();
    Logging::debug("articleedit POST request", $json);

    if (isset($json['add-article'])) {
        echo addArticle($json);
    } else if (isset($json['update-article'])) {
        echo updateArticle($json);
    }
} else {
    if (!Authenticate::isLoggedIn()) {
        Util::redirectTo(Route::LOGIN);
    } else {
        echo (new ArticleEdit())->renderRoute();
    }
}
