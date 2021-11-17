<?php

################################
## Joël Piguet - 2021.11.17 ###
##############################

namespace routes;

/**
 * Bundled constants for Article edit
 */
class ArtEdit
{
    const ARTICLE_KEY = 'article-name';
    const DATE_EXP_KEY = 'expiration-date';
    const LOCATION_KEY = 'location';
    const COMMENTS_KEY = 'comments';
}

class ArtEditRoute extends BaseRoute
{

    const ARTICLE_ADD_EMPTY = "Il faut donner un nom à l'article à ajouter.";
    const ARTICLE_NAME_TOO_SHORT = "Le nom de l'article doit au moins compter %s caractères.";
    const ARTICLE_NAME_TOO_LONG = "Le nom de l'article ne doit pas dépasser %s caractères.";
    const COMMENTS_NAME_TOO_LONG = "Les commentaires ne doivent pas dépasser %s caractèrs.";
    const LOCATION_EMPTY = "Il est nécessaire de préciser l'emplacement.";
    const LOCATION_NAME_TOO_LONG = "L'emplacement ne doit pas dépasser %s caractères.";

    const NAME_MAX_LENGTH = 20;
    const NAME_MIN_LENGHT = 6;
    const LOCATION_MAX_LENGHT = 40;
    const COMMENTS_MAX_LENGHT = 240;

    function __construct()
    {
        parent::__construct('article_edit_template');
    }

    public function getBodyContent(): string
    {
        $values = [
            ArtEdit::ARTICLE_KEY => '',
            ArtEdit::DATE_EXP_KEY => '',
            ArtEdit::LOCATION_KEY => '',
            ArtEdit::COMMENTS_KEY => '',
        ];
        $errors = [];


        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_POST['new-article'])) {

                $values[ArtEdit::ARTICLE_KEY] = trim($_POST[ArtEdit::ARTICLE_KEY]) ?? '';
                $values[ArtEdit::LOCATION_KEY] = trim($_POST[ArtEdit::LOCATION_KEY]) ?? '';
                $values[ArtEdit::DATE_EXP_KEY] = trim($_POST[ArtEdit::DATE_EXP_KEY] ?? '');
                $values[ArtEdit::COMMENTS_KEY] = trim($_POST[ArtEdit::COMMENTS_KEY]) ?? '';

                if ($this->validate_article_name($values, $errors) && $this->validate_location($values, $errors) && $this->validate_exp_date($values, $errors) && $this->validate_comments($values, $errors)) {
                }

                // var_dump($errors);
                // throw new Exception('Not implemented');
                // if ($this->addArticle($user->getId(), $form_errors)) {
                //     throw new Exception('Display an info Alert if successful.');
                // }
            }
        }

        return $this->renderTemplate([
            'errors' => $errors,
            'values' => $values,
        ]);
    }


    /**
     * Article name validation. Article name must not be empty, exceed a set length and under a set number of caracters.
     * 
     * @param array $values Values array passed by reference.
     * @param array &$errors Error array passed by reference to store error message.
     * @return bool True if validated.
     */
    private function validate_article_name(array &$values, array &$errors): bool
    {
        $article_name = $values(ArtEdit::ARTICLE_KEY);
        if ($article_name === '') {
            $errors[ArtEdit::ARTICLE_KEY] = ArtEditRoute::ARTICLE_ADD_EMPTY;
            return false;
        }

        if (strlen($article_name) < ArtEditRoute::NAME_MIN_LENGHT) {
            $errors[ArtEdit::ARTICLE_KEY] = sprintf(ArtEditRoute::ARTICLE_NAME_TOO_SHORT, ArtEditRoute::NAME_MIN_LENGHT);
        }
        if (strlen($article_name) > ArtEditRoute::NAME_MAX_LENGTH) {
            $errors[ArtEdit::ARTICLE_KEY] = sprintf(ArtEditRoute::ARTICLE_NAME_TOO_LONG, ArtEditRoute::NAME_MAX_LENGTH);
            return false;
        }
        return true;
    }

    /**
     * Date validation. Date must not be empty and correspond to format dd/mm/yyyy.
     * 
     * @param array $values Values array passed by reference.
     * @param array &$errors Error array passed by reference to store error message.
     * @return bool True if validated.
     */
    private function validate_exp_date(array &$values, array &$errors): bool
    {
        $errors[ArtEdit::DATE_EXP_KEY] = 'debug date error';
        return false;
    }

    /**
     * Location validation. Location must not be empty and under a set number of caracters.
     * 
     * @param array $values Values array passed by reference.
     * @param array &$errors Error array passed by reference to store error message.
     * @return bool True if validated.
     */
    private function validate_location(array &$values, array &$errors): bool
    {
        $location = $values[ArtEdit::LOCATION_KEY];
        if ($location === '') {
            $errors['location'] = ArtEditRoute::LOCATION_EMPTY;
            return false;
        }
        if (strlen($location) > ArtEditRoute::LOCATION_MAX_LENGHT) {
            $errors['location'] = sprintf(ArtEditRoute::LOCATION_NAME_TOO_LONG, ArtEditRoute::LOCATION_MAX_LENGHT);
            return false;
        }
        return true;
    }

    /**
     * Comments validation. Comments can be empty string but be under a set number of caracters.
     * 
     * @param array $values Values array passed by reference.
     * @param array &$errors Error array passed by reference to store error message.
     * @return bool True if validated.
     */
    private function validate_comments(array &$values, array &$errors): bool
    {
        $comments = $values[ArtEdit::COMMENTS_KEY];
        if (strlen($comments) > ArtEditRoute::COMMENTS_MAX_LENGHT) {
            $errors[ArtEdit::COMMENTS_KEY] = sprintf(ArtEditRoute::COMMENTS_NAME_TOO_LONG, ArtEditRoute::COMMENTS_MAX_LENGHT);
            return false;
        }
        return true;
    }
}
