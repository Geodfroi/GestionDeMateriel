<?php

################################
## Joël Piguet - 2021.11.11 ###
##############################

namespace routes;

abstract class BaseRoute
{
    private string $redirectUri = "";

    public function requestRedirect(string $uri)
    {
        $this->redirectUri = $uri;

        //The header php function is used for redirection
        header("Location: $uri", true);
    }

    public abstract function getPageContent(): string;

    public function getPageTitle(): string
    {
        return DEFAULT_PAGE_TITLE;
    }

    public function isRedirecting(): bool
    {
        return strlen($this->redirectUri) > 0;
    }
}
