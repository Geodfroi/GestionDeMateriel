<?php

################################
## Joël Piguet - 2021.11.25 ###
##############################

namespace routes;

use helpers\Util;

/**
 * Abstract class of all routes containing common utility functions.
 */
abstract class BaseRoute
{
    private string $redirectUri = "";

    /**
     * Link a Route with its designated template in the constructor.
     * 
     * @param string $templateName Template to use in renderTemplate call.
     * @param string $route Route's name to store in SESSION global variable (used to highlight current page in nav bar).
     */
    public function __construct(string $templateName, string $route)
    {
        $_SESSION['route'] = $route;
        $this->templateName = $templateName;
    }

    /**
     * Send a header to the browser requesting a redirection to the path provided.
     * 
     * @param string $uri The redirection path. Use the constants in Routes class to avoir typing mistakes.
     */
    public function requestRedirect(string $uri)
    {
        $this->redirectUri = $uri;

        //The header php function will send a header message to the browser, here signaling for redirection.
        header("Location: $uri", true);
    }

    /**
     * Executes the custom php code in the route and returns html dynamically.
     * 
     * @return string HTML content to be inserted into the main template body.
     */
    public abstract function getBodyContent(): string;

    /**
     * Allow to customize a title for each path. If the function is not overloaded in a route, a default title is return instead.
     * 
     * @return string Title to be displayed in browser.
     */
    public function getHeaderTitle(): string
    {
        return APP_NAME;
    }

    /**
     * As a redirecting uri has been provided. Further rendering of this route is to be cancelled.
     */
    public function isRedirecting(): bool
    {
        return strlen($this->redirectUri) > 0;
    }

    /** 
     * Load a php template in memory and returns a content string.
     * 
     * @param array $data The variables to be used in php templates.
     */
    protected function renderTemplate(array $data = []): string
    {
        return Util::renderTemplate($this->templateName, $data);
    }
}
