<?php

################################
## Joël Piguet - 2021.11.16 ###
##############################

namespace routes;

use Exception;
use helpers\Authenticate;
use helpers\Database;
use models\Article;

const DEBUG_LIMIT = 12;
const DEBUG_OFFSET = 0;

const ARTICLE_ADD_EMPTY = "Il faut donner un nom à l'article à ajouter.";
const ARTICLE_NAME_TOO_LONG = "Le nom de l'article ne doit pas dépasser %s caractères.";

const COMMENTS_NAME_TOO_LONG = "Les commentaires ne doivent pas dépasser %s caractèrs.";
const LOCATION_EMPTY = "Il est nécessaire de préciser l'emplacement.";
const LOCATION_NAME_TOO_LONG = "L'emplacement ne doit pas dépasser %s caractères.";

/**
 * Route class containing behavior linked to user_template. This route display an user Article list and allows create-remove-update tasks on articles list.
 */
class ArticlesRoute extends BaseRoute
{
    public function __construct()
    {
        parent::__construct("articles_template");
    }

    public function getBodyContent(): string
    {
        if (!Authenticate::isLoggedIn()) {
            $this->requestRedirect(Routes::LOGIN);
        }

        $articles = [];
        $user = Authenticate::getUser();
        $form_errors = [];

        if (isset($user)) {
            $articles = Database::getInstance()->getUserArticles($user->getId(), DEBUG_LIMIT, DEBUG_OFFSET);
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_POST['new-article'])) {

                $article_name  = trim($_POST['article-name']) ?? '';
                $location = trim($_POST['location']) ?? '';
                $comments = trim($_POST['comments']) ?? '';

                if ($this->validate_article_name($article_name, $form_errors) && $this->validate_location($location, $form_errors) && $this->validate_comments($comments, $form_errors)) {
                }
                throw new Exception('Not implemented');
                // if ($this->addArticle($user->getId(), $form_errors)) {
                //     throw new Exception('Display an info Alert if successful.');
                // }
            }
        }


        return $this->renderTemplate([
            'articles' =>  $articles,
            'form_errors' => $form_errors
        ]);
    }

    public function validate_article_name(string $article_name, array &$errors): bool
    {
        if ($article_name === '') {
            $errors['article-name'] = ARTICLE_ADD_EMPTY;
            return false;
        }
        if (strlen($article_name) > Article::NAME_MAX_LENGTH) {
            $errors['article-name'] = sprintf(ARTICLE_NAME_TOO_LONG, Article::NAME_MAX_LENGTH);
            return false;
        }
        return true;
    }

    public function validate_location(string $location, array &$errors): bool
    {
        if ($location === '') {
            $errors['location'] = LOCATION_EMPTY;
            return false;
        }
        if (strlen($location) > Article::LOCATION_MAX_LENGHT) {
            $errors['location'] = sprintf(LOCATION_NAME_TOO_LONG, Article::LOCATION_MAX_LENGHT);
            return false;
        }
        return true;
    }

    public function validate_comments(string $comments, array &$errors): bool
    {
        if (strlen($comments) > Article::COMMENTS_MAX_LENGHT) {
            $errors['comments'] = sprintf(COMMENTS_NAME_TOO_LONG, Article::COMMENTS_MAX_LENGHT);
            return false;
        }
        return true;
    }
}


    // /**
    //  * Create an article instance from form and add it to the database. 
    //  * 
    //  * @param int $user_id Id of the owner.
    //  * @param int $expirationDate Validated expiration date.
    //  * @return bool True if the data could be inserted in database.
    //  */
    // function addArticle(int $user_id, array &$errors): bool
    // {


    //     // $article = Article::fromForm($user_id, article_name, location, expirationDate);
    //     // if (Database::getInstance()->insertArticle($article)) {
    //     // }
    // }