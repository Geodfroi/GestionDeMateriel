<?php

################################
## Joël Piguet - 2021.11.23 ###
##############################

namespace routes;

use helpers\Authenticate;
use helpers\Database;
use models\Article;

use DateTime;

class ArticleEdit extends BaseRoute
{


    const ARTICLE_ADD_EMPTY = "Il faut donner un nom à l'article à ajouter.";
    const ARTICLE_NAME_TOO_SHORT = "Le nom de l'article doit compter au moins  %s caractères.";
    const ARTICLE_NAME_TOO_LONG = "Le nom de l'article ne doit pas dépasser %s caractères.";
    const COMMENTS_NAME_TOO_LONG = "Les commentaires ne doivent pas dépasser %s caractèrs.";
    const LOCATION_EMPTY = "Il est nécessaire de préciser l'emplacement.";
    const LOCATION_NAME_TOO_SHORT = "L'emplacement doit compter au moins %s caractères.";
    const LOCATION_NAME_TOO_LONG = "L'emplacement ne doit pas dépasser %s caractères.";
    const DATE_EMPTY = "Il est nécessaire d'entrer la date d'expiration.";
    const DATE_PAST = "La date fournie doit être dans le future.";
    const DATE_INVALID = "La date fournie est invalide.";
    const DATE_FUTURE = "La date fournie est trop loin dans le future.";

    const NAME_MIN_LENGHT = 6;
    const NAME_MAX_LENGTH = 20;
    const LOCATION_MIN_LENGHT = 6;
    const LOCATION_MAX_LENGHT = 40;
    const COMMENTS_MAX_LENGHT = 240;
    const DATE_FUTURE_LIMIT = '2050-01-01';

    function __construct()
    {
        parent::__construct('article_edit_template');
    }

    public function getBodyContent(): string
    {
        // An invisible field in the form will hold the id value if the form is used to update an existing Article.
        $errors = [];

        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            if (isset($_GET['update'])) {

                // load input fields with existing values to update item.
                $article = Database::getInstance()->getArticleById($_GET['update']);
                $article_id = $article->getId();
                $article_name = $article->getArticleName();
                $exp_date_str = $article->getExpirationDate()->format('Y-m-d');
                $location = $article->getLocation();
                $comments = $article->getComments();
            }
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            if (isset($_POST['new-article'])) {
                if ($this->validate_inputs($article_name, $location, $exp_date_str, $comments, $errors)) {

                    $user_id = Authenticate::getUser()->getId();

                    $article = Article::fromForm($user_id, $article_name, $location, $exp_date_str, $comments);

                    if (Database::getInstance()->insertArticle($article)) {
                        $this->requestRedirect(Routes::ARTICLES . '?alert=added_success');
                    } else {
                        $this->requestRedirect(Routes::ARTICLES . '?alert=added_failure');
                    }
                }
            } else if (isset($_POST['update-article'])) {

                $article_id  = $_POST['id'];

                if ($this->validate_inputs($article_name, $location, $exp_date_str, $comments, $errors)) {
                    $user_id = Authenticate::getUser()->getId();

                    $article = Database::getInstance()->getArticleById($_POST['id']);
                    $article->updateFields($article_name, $location, $exp_date_str, $comments);

                    error_log($article->__toString());

                    if (Database::getInstance()->updateArticle($article)) {
                        $this->requestRedirect(Routes::ARTICLES . '?alert=updated_success');
                    } else {
                        $this->requestRedirect(Routes::ARTICLES . '?alert=updated_failure');
                    }
                }
            }
        }

        $values = [
            'id' => $article_id ?? 'no-id',
            'article-name' => $article_name ?? '',
            'expiration-date' => $exp_date_str ?? '',
            'location' => $location ?? '',
            'comments' => $comments ?? '',
        ];

        return $this->renderTemplate([
            'errors' => $errors,
            'values' => $values
        ]);
    }

    /**
     * Validate form inputs before using it to add/update article.
     * 
     * @param array &$string $article_name Article name by reference.
     * @param string &$location Article's location within the school by reference.
     * @param string &$exp_date Expiration date.
     * @param string &$comments Comments to be attached to the reminder by reference.
     * @param array &$errors Error array passed by reference to store error message.
     * @return bool True if validation is successful.
     */
    private function validate_inputs(?string &$article_name, ?string &$location, ?string &$exp_date, ?string &$comments, array &$errors): bool
    {
        // Avoid && or || between conditions because all validation methods must be run without short-circuit.
        $validated   = true;
        if (!$this->validate_article_name($article_name, $errors)) {
            $validated  = false;
        }
        if (!$this->validate_location($location, $errors)) {
            $validated  = false;
        }
        if (!$this->validate_exp_date($exp_date, $errors)) {
            $validated  = false;
        }
        if (!$this->validate_comments($comments, $errors)) {
            $validated  = false;
        }
        return $validated;
    }

    /**
     * Article name validation. Article name must not be empty, exceed a set length and under a set number of caracters.
     * 
     * @param array &$string $article_name Article name by reference.
     * @param array &$errors Error array passed by reference to store error message.
     * @return bool True if validated.
     */
    private function validate_article_name(?string &$article_name, array &$errors): bool
    {
        $article_name = trim($_POST['article-name']) ?? '';

        if ($article_name === '') {
            $errors['article-name'] = ArticleEdit::ARTICLE_ADD_EMPTY;
            return false;
        }

        if (strlen($article_name) < ArticleEdit::NAME_MIN_LENGHT) {
            $errors['article-name'] = sprintf(ArticleEdit::ARTICLE_NAME_TOO_SHORT, ArticleEdit::NAME_MIN_LENGHT);
            return false;
        }

        if (strlen($article_name) > ArticleEdit::NAME_MAX_LENGTH) {
            $errors['article-name'] = sprintf(ArticleEdit::ARTICLE_NAME_TOO_LONG, ArticleEdit::NAME_MAX_LENGTH);
            return false;
        }
        return true;
    }

    /**
     * Date validation. Date must not be empty and correspond to format yyyy-mm-dd
     * 
     * @param string &$validated_date Validated expiration date.
     * @param array &$errors Error array passed by reference to store error message.
     * @return bool True if validated.
     */
    private function validate_exp_date(?string &$date, array &$errors): bool
    {
        $date = trim($_POST['expiration-date'] ?? '');

        if ($date === '') {
            $errors['expiration-date'] =  ArticleEdit::DATE_EMPTY;
            return false;
        }

        $validated_date = DateTime::createFromFormat('Y-m-d', $date);
        $date = $validated_date->format('Y-m-d');

        static $future_limit;
        if (is_null($future_limit)) {
            $future_limit = DateTime::createFromFormat('Y-m-d', ArticleEdit::DATE_FUTURE_LIMIT);
        }

        if ($validated_date) {

            if ($validated_date < new DateTime()) {
                $errors['expiration-date'] =  ArticleEdit::DATE_PAST;
                return false;
            }

            if ($validated_date >= $future_limit) {
                $errors['expiration-date'] =  ArticleEdit::DATE_FUTURE;
                return false;
            }

            return true;
        }

        $errors['expiration-date'] =  ArticleEdit::DATE_INVALID;
        return false;
    }

    /**
     * Location validation. Location must not be empty and under a set number of caracters.
     * 
     * @param string &$location Article's location within the school by reference.
     * @param array &$errors Error array passed by reference to store error message.
     * @return bool True if validated.
     */
    private function validate_location(?string &$location, array &$errors): bool
    {
        $location = trim($_POST['location']) ?? '';

        if ($location === '') {
            $errors['location'] = ArticleEdit::LOCATION_EMPTY;
            return false;
        }

        if (strlen($location) < ArticleEdit::LOCATION_MIN_LENGHT) {
            $errors['location'] = sprintf(ArticleEdit::LOCATION_NAME_TOO_SHORT, ArticleEdit::LOCATION_MIN_LENGHT);
            return false;
        }

        if (strlen($location) > ArticleEdit::LOCATION_MAX_LENGHT) {
            $errors['location'] = sprintf(ArticleEdit::LOCATION_NAME_TOO_LONG, ArticleEdit::LOCATION_MAX_LENGHT);
            return false;
        }
        return true;
    }

    /**
     * Comments validation. Comments can be empty string but be under a set number of caracters.
     * 
     * @param string &$comments Comments to be attached to the reminder by reference.
     * @param array &$errors Error array passed by reference to store error message.
     * @return bool True if validated.
     */
    private function validate_comments(?string &$comments, array &$errors): bool
    {
        $comments = trim($_POST['comments']) ?? '';

        if (strlen($comments) > ArticleEdit::COMMENTS_MAX_LENGHT) {
            $errors['comments'] = sprintf(ArticleEdit::COMMENTS_NAME_TOO_LONG, ArticleEdit::COMMENTS_MAX_LENGHT);
            return false;
        }
        return true;
    }
}
