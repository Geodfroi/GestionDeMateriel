<?php

################################
## Joël Piguet - 2021.11.17 ###
##############################

namespace routes;


class ArticleEdit extends BaseRoute
{
    function __construct()
    {
        parent::__construct('article_edit_template');
    }

    public function getBodyContent(): string
    {
        return $this->renderTemplate();
    }
}
