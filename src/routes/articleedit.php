<?php

################################
## Joël Piguet - 2022.01.10 ###
##############################

namespace app\routes;

use app\constants\Alert;
use app\constants\AlertType;
use app\constants\LogInfo;
use app\constants\Route;
use app\helpers\Authenticate;
use app\helpers\Database;
use app\helpers\Logging;
use app\helpers\Validation;
use app\models\Article;

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

            // load input fields with existing values to update item.
            $article = Database::articles()->queryById($_GET['update']);
            $article_id = $article->getId();
            $article_name = $article->getArticleName();
            $exp_date_str = $article->getExpirationDate()->format('Y-m-d');
            $location = $article->getLocation();
            $comments = $article->getComments();

            goto end;
        }

        if (isset($_POST['new-article'])) {
            if ($this->validateInputs($article_name, $location, $exp_date_str, $comments)) {

                $user_id = Authenticate::getUserId();
                $article = Article::fromForm($user_id, $article_name, $location, $exp_date_str, $comments);

                $article_id = Database::articles()->insert($article);
                if ($article_id) {
                    Logging::info(LogInfo::ARTICLE_CREATED, ['user-id' => $user_id, 'article-id' => $article_id]);
                    return $this->requestRedirect(Route::ART_TABLE, AlertType::SUCCESS, ALERT::ARTICLE_ADD_SUCCESS);
                } else {
                    return $this->requestRedirect(Route::ART_TABLE, AlertType::FAILURE, ALERT::ARTICLE_ADD_FAILURE);
                }
            }
            goto end;
        }

        if (isset($_POST['update-article'])) {

            $article_id  = $_POST['id'];

            if ($this->validateInputs($article_name, $location, $exp_date_str, $comments,)) {
                $user_id = Authenticate::getUserId();

                $article = Database::articles()->queryById($_POST['id']);
                $article->updateFields($article_name, $location, $exp_date_str, $comments);

                if (Database::articles()->update($article)) {
                    Logging::info(LogInfo::ARTICLE_UPDATED, ['user-id' => $user_id, 'article-id' => $article->getId()]);
                    return $this->requestRedirect(Route::ART_TABLE, AlertType::SUCCESS, ALERT::ARTICLE_UPDATE_SUCCESS);
                } else {
                    return $this->requestRedirect(Route::ART_TABLE, AlertType::FAILURE, ALERT::ARTICLE_UPDATE_FAILURE);
                }
            }
        }

        end:

        return $this->renderTemplate([
            'article_name' => $article_name ?? '',
            'comments' => $comments ?? '',
            'expiration_date' => $exp_date_str ?? '',
            'id' => $article_id ?? 'no-id',
            'location' => $location ?? '',
        ]);
    }

    /**
     * Validate form inputs before using it to add/update article.
     * 
     * @param array &$string $article_name Article name by reference.
     * @param string &$location Article's location within the school by reference.
     * @param string &$exp_date Expiration date.
     * @param string &$comments Comments to be attached to the reminder by reference.
     * @return bool True if validation is successful.
     */
    private function validateInputs(?string &$article_name, ?string &$location, ?string &$exp_date, ?string &$comments): bool
    {
        // Avoid && or || between conditions because all validation methods must be run without short-circuit to properly display all error messages.
        $validated  = true;
        if (!Validation::validateArticleName($this, $article_name)) {
            $validated  = false;
        }
        if (!Validation::validateLocation($this, $location)) {
            $validated  = false;
        }
        if (!Validation::validateExpirationDate($this, $exp_date)) {
            $validated  = false;
        }
        if (!Validation::validateComments($this, $comments)) {
            $validated  = false;
        }
        return $validated;
    }
}
