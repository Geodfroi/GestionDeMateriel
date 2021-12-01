<?php

################################
## Joël Piguet - 2021.12.01 ###
##############################

namespace routes;

use helpers\Authenticate;
use helpers\Database;
use models\Article;

use DateTime;

class ArticleEdit extends BaseRoute
{
    function __construct()
    {
        parent::__construct(ART_EDIT_TEMPLATE, ART_EDIT);
    }

    public function getBodyContent(): string
    {
        // An invisible field in the form will hold the id value if the form is used to update an existing Article.

        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            if (isset($_GET['update'])) {

                // load input fields with existing values to update item.
                $article = Database::articles()->queryById($_GET['update']);
                $article_id = $article->getId();
                $article_name = $article->getArticleName();
                $exp_date_str = $article->getExpirationDate()->format('Y-m-d');
                $location = $article->getLocation();
                $comments = $article->getComments();
            }
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            if (isset($_POST['new-article'])) {
                if ($this->validateInputs($article_name, $location, $exp_date_str, $comments)) {

                    $user_id = Authenticate::getUserId();

                    $article = Article::fromForm($user_id, $article_name, $location, $exp_date_str, $comments);

                    if (Database::articles()->insert($article)) {
                        $this->requestRedirect(ART_TABLE . '?alert=added_success');
                    } else {
                        $this->requestRedirect(ART_TABLE . '?alert=added_failure');
                    }
                }
            } else if (isset($_POST['update-article'])) {

                $article_id  = $_POST['id'];

                if ($this->validateInputs($article_name, $location, $exp_date_str, $comments,)) {
                    $user_id = Authenticate::getUserId();

                    $article = Database::articles()->queryById($_POST['id']);
                    $article->updateFields($article_name, $location, $exp_date_str, $comments);

                    if (Database::articles()->update($article)) {
                        $this->requestRedirect(ART_TABLE . '?alert=updated_success');
                    } else {
                        $this->requestRedirect(ART_TABLE . '?alert=updated_failure');
                    }
                }
            }
        }

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
        // Avoid && or || between conditions because all validation methods must be run without short-circuit.
        $validated   = true;
        if (!$this->validateArticleName($article_name)) {
            $validated  = false;
        }
        if (!$this->validateLocation($location)) {
            $validated  = false;
        }
        if (!$this->validateExpirationDate($exp_date)) {
            $validated  = false;
        }
        if (!$this->validateComments($comments)) {
            $validated  = false;
        }
        return $validated;
    }

    /**
     * Article name validation. Article name must not be empty, exceed a set length and under a set number of caracters.
     * 
     * @param array &$string $article_name Article name by reference.
     * @return bool True if validated.
     */
    private function validateArticleName(?string &$article_name): bool
    {
        $article_name = trim($_POST['article-name']) ?? '';

        if ($article_name === '') {
            $this->setError('article-name', ARTICLE_ADD_EMPTY);
            return false;
        }

        if (strlen($article_name) < ARTICLE_NAME_MIN_LENGHT) {
            $this->setError('article-name', sprintf(ARTICLE_NAME_TOO_SHORT, ARTICLE_NAME_MIN_LENGHT));
            return false;
        }

        if (strlen($article_name) > ARTICLE_NAME_MAX_LENGTH) {
            $this->setError('article-name', sprintf(ARTICLE_NAME_TOO_LONG, ARTICLE_NAME_MAX_LENGTH));
            return false;
        }
        return true;
    }

    /**
     * Date validation. Date must not be empty and correspond to format yyyy-mm-dd
     * 
     * @param string &$validated_date Validated expiration date.
     * @return bool True if validated.
     */
    private function validateExpirationDate(?string &$date): bool
    {
        $date = trim($_POST['expiration-date'] ?? '');

        if ($date === '') {
            $this->setError('expiration-date', DATE_EMPTY);
            return false;
        }

        $validated_date = DateTime::createFromFormat('Y-m-d', $date);
        $date = $validated_date->format('Y-m-d');

        static $future_limit;
        if (is_null($future_limit)) {
            $future_limit = DateTime::createFromFormat('Y-m-d', ARTICLE_DATE_FUTURE_LIMIT);
        }

        if ($validated_date) {

            if ($validated_date < new DateTime()) {
                $this->setError('expiration-date', DATE_PAST);
                return false;
            }

            if ($validated_date >= $future_limit) {
                $this->setError('expiration-date', DATE_FUTURE);
                return false;
            }

            return true;
        }

        $this->setError('expiration-date', DATE_INVALID);
        return false;
    }

    /**
     * Location validation. Location must not be empty and under a set number of caracters.
     * 
     * @param string &$location Article's location within the school by reference.
     * @return bool True if validated.
     */
    private function validateLocation(?string &$location): bool
    {
        $location = trim($_POST['location']) ?? '';

        if ($location === '') {
            $this->setError('location', LOCATION_EMPTY);
            return false;
        }

        if (strlen($location) < ARTICLE_LOCATION_MIN_LENGHT) {
            $this->setError('location', sprintf(LOCATION_NAME_TOO_SHORT, ARTICLE_LOCATION_MIN_LENGHT));
            return false;
        }

        if (strlen($location) > ARTICLE_LOCATION_MAX_LENGHT) {
            $this->setError('location', sprintf(LOCATION_NAME_TOO_LONG, ARTICLE_LOCATION_MAX_LENGHT));
            return false;
        }
        return true;
    }

    /**
     * Comments validation. Comments can be empty string but be under a set number of caracters.
     * 
     * @param string &$comments Comments to be attached to the reminder by reference.
     * @return bool True if validated.
     */
    private function validateComments(?string &$comments): bool
    {
        $comments = trim($_POST['comments']) ?? '';

        if (strlen($comments) > ARTICLE_COMMENTS_MAX_LENGHT) {
            $this->setError('comments', sprintf(COMMENTS_NAME_TOO_LONG, ARTICLE_COMMENTS_MAX_LENGHT));
            return false;
        }
        return true;
    }
}
